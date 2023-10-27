<template>
  <div>
    <AppLayout title="Crear Oportunidad">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('oportunities.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear Oportunidad</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4 mx-3">
          <div>
            <label>Nombre de la oportunidad *</label>
            <input v-model="form.name" class="input" type="text" />
            <InputError :message="form.errors.name" />
          </div>
          <div class="relative">
            <i :class="getColorStatus(form.status)" class="fa-solid fa-circle text-xs top-1 left-16 absolute z-30"></i>
            <label>Estatus</label> <br />
            <div class="flex items-center space-x-4">
              <el-select class="lg:w-1/2 mt-2" v-model="form.status" clearable filterable
                placeholder="Seleccionar estatus" no-data-text="No hay estatus registrados"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
                  <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                  <span style="float: center; margin-left: 5px; font-size: 13px">{{
                    item.label
                  }}</span>
                </el-option>
              </el-select>
            </div>
            <InputError :message="form.errors.status" />
          </div>
          <div>
            <label>Vendedor</label>
            <el-select v-model="form.seller_id" clearable filterable placeholder="Seleccione"
              no-data-text="No hay vendedores registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="seller in users.filter(
                (user) => user.employee_properties?.department == 'Ventas'
              )" :key="seller" :label="seller.name" :value="seller.id" />
            </el-select>
          </div>
          <label class="inline-flex items-center">
            <Checkbox v-model:checked="form.is_new_company" @change="handleChecked"
              class="bg-transparent disabled:border-gray-400" />
            <span class="ml-2 text-xs">Nuevo cliente</span>
          </label>
          <div v-if="form.is_new_company">
            <label>Contacto *</label>
            <input v-model="form.contact" class="input" type="text" />
            <InputError :message="form.errors.contact" />
          </div>
          <div v-if="!form.is_new_company" class="flex space-x-2 justify-between">
            <div class="w-1/2">
              <label>Cliente *</label> <br />
              <el-select v-model="form.company_id" clearable filterable placeholder="Seleccione"
                no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="company in companies" :key="company" :label="company.business_name"
                  :value="company.id" />
              </el-select>
              <InputError :message="form.errors.company_id" />
            </div>
            <div class="w-1/2">
              <label>Sucursal *</label> <br />
              <el-select @change="saveCompanyBranchAddress" v-model="company_branch" clearable filterable
                placeholder="Seleccione" no-data-text="No hay sucursales registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="company_branch in companies.find(
                  (item) => item.id == form.company_id
                )?.company_branches" :key="company_branch" :label="company_branch.name" :value="company_branch.name" />
              </el-select>
            </div>
            <div class="w-1/2">
              <label>Contacto *</label> <br />
              <el-select v-model="form.contact" clearable filterable placeholder="Seleccione"
                no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="contact in company_branch_obj?.contacts" :key="contact" :label="contact.name"
                  :value="contact.name" />
              </el-select>
            </div>
          </div>
          <div class="lg:flex pt-3">
            <div class="lg:w-1/2 mt-2 lg:mt-0">
              <label class="block">Fecha de inicio *</label>
              <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha de inicio *" format="YYYY/MM/DD"
                value-format="YYYY-MM-DD" />
              <InputError :message="form.errors.start_date" />
            </div>
            <div class="lg:w-1/2 mt-2 lg:mt-0">
              <label class="block">Fecha estimada de cierre *</label>
              <el-date-picker v-model="form.estimated_finish_date" type="date" placeholder="Fecha estimada de cierre *"
                format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
              <InputError :message="form.errors.estimated_finish_date" />
            </div>
          </div>
          <div>
            <label>Descripción</label>
            <RichText @content="updateDescription($event)" v-model="form.description" />
          </div>
          <div class="ml-2 mt-2 col-span-full flex">
            <FileUploader @files-selected="this.form.media = $event" />
          </div>

          <div class="flex justify-between items-center space-x-4">
            <div class="w-full">
              <div class="flex justify-between items-center mx-2">
                <label>Etiquetas</label>
                <button @click="showTagFormModal = true" type="button"
                  class="rounded-full border border-primary w-4 h-4 flex items-center justify-center">
                  <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                </button>
              </div>
              <el-select v-model="form.tags" clearable placeholder="Seleccione" multiple class="w-full"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="(item, index) in tags.data" :key="item.id" :label="item.name" :value="item.id">
                  <Tag :name="item.name" :color="item.color" />
                </el-option>
              </el-select>
            </div>
            <div class="w-full">
              <label>Probabilidad %</label>
              <input v-model="form.probability" class="input" type="number" min="0" max="100" />
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <div class="lg:w-1/2 relative">
              <i :class="getColorPriority(form.priority)"
                class="fa-solid fa-circle text-xs top-1 left-20 absolute z-30"></i>
              <label class="block">Prioridad</label>
              <div class="flex items-center space-x-4">
                <el-select class="lg:w-1/2" v-model="form.priority" clearable filterable placeholder="Seleccione"
                  no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                  <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
                    <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                    <span style="float: center; margin-left: 5px; font-size: 13px">{{
                      item.label
                    }}</span>
                  </el-option>
                </el-select>
                <InputError :message="form.errors.priority" />
              </div>
            </div>

            <div v-if="form.status == 'Perdida'" class="lg:w-1/2">
              <label>Causa oportunidad perdida
                <el-tooltip content="Escribe la causa por la cual se PERDIÓ esta oportunidad" placement="right">
                  <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
                </el-tooltip>
              </label>
              <input v-model="form.lost_oportunity_razon" class="input" type="text" />
              <InputError :message="form.errors.lost_oportunity_razon" />
            </div>
          </div>
          <div class="lg:w-1/2">
            <div class="flex items-center space-x-2">
              <label>Valor de oportunidad *</label>
              <el-tooltip content="Monto estimado que se espera generar si se cierra esta oportunidad" placement="right">
                <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                  <i class="fa-solid fa-info text-primary text-[7px]"></i>
                </div>
              </el-tooltip>
            </div>
            <input v-model="form.amount" class="input" type="number" min="0" step="0.01" />
            <InputError :message="form.errors.amount" />
          </div>

          <h2 class="font-bold text-sm my-2 col-span-full">Acceso al proyecto</h2>
          <div class="col-span-full text-sm">
            <div class="my-1">
              <input v-model="typeAccessProject" value="Public"
                class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
                name="typeAccessProject">
              <b>Público</b>
              <p class="text-[#9A9A9A] ml-7 text-xs">Los usuarios del portal solo pueden ver, seguir y comentar, mientras
                que los usuarios del proyecto tendrán acceso directo.</p>
            </div>
            <div class="my-1">
              <input v-model="typeAccessProject" value="Private"
                class="checked:bg-primary focus:text-primary focus:ring-primary border-black mr-3" type="radio"
                name="typeAccessProject">
              <b>Privado</b>
              <p class="text-[#9A9A9A] ml-7 text-xs">Solo los usuarios de proyecto pueden ver y acceder a este proyecto
              </p>
            </div>
          </div>

          <section class="rounded-[10px] py-12 mx-1 mt-5 max-h-[540px] col-span-full bg-[#CCCCCC]">
            <div class="flex px-16 mb-8">
              <div v-if="typeAccessProject === 'Private'" class="w-full">
                <h2 class="font-bold text-sm my-2 ml-2 col-span-full">Asignar participantes </h2>
                <el-select @change="addToSelectedUsers" filterable clearable placeholder="Seleccionar usuario"
                  class="w-1/2" no-data-text="No hay más usuarios para añadir"
                  no-match-text="No se encontraron coincidencias">
                  <el-option v-for="(item, index) in availableUsersToPermissions" :key="item.id" :label="item.name"
                    :value="item.id" />
                </el-select>
              </div>
              <ThirthButton v-if="typeAccessProject === 'Public'" type="button" class="ml-auto self-start"
                @click.stop="editAccesFlag = !editAccesFlag">
                {{ editAccesFlag ? 'Actualizar' : 'Editar' }}
              </ThirthButton>
            </div>
            <div class="flex justify-between px-5 mt-4">
              <div class="w-full">
                <div class="flex">
                  <h2 class="font-bold border-b border-[#9A9A9A] w-2/3 pl-3">Usuarios</h2>
                  <h2 class="font-bold border-b border-[#9A9A9A] w-1/3">Permisos</h2>
                </div>
                <div class="pl-3 overflow-y-auto min-h-[100px] max-h-[340px]">
                  <template v-for="user in form.selectedUsersToPermissions" :key="user.id">
                    <div v-if="user.id !== 1" class="flex mt-2 border-b border-[#9A9A9A]">
                      <div class="w-2/3 flex space-x-2">
                        <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-12">
                          <img class="h-10 w-10 rounded-full object-cover" :src="user.profile_photo_url"
                            :alt="user.name" />
                        </div>
                        <div class="text-sm w-full">
                          <p>{{ user.name }}</p>
                          <p v-if="user.employee_properties">{{ 'Depto. ' + user.employee_properties?.department }}</p>
                          <p v-else>Super admin</p>
                        </div>
                      </div>

                      <div class="w-1/3 flex items-center justify-between">
                        <div class="space-y-1 mb-2">
                          <label class="flex items-center">
                            <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                              v-model="user.permissions[0]" :checked="user.permissions[0]" />{{ permissions }}
                            <span
                              :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                              class="ml-2 text-xs">
                              Crea actividades
                            </span>
                          </label>
                          <label class="flex items-center">
                            <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                              v-model="user.permissions[1]" :checked="user.permissions[1]" />
                            <span
                              :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                              class="ml-2 text-xs">Ver</span>
                          </label>
                          <label class="flex items-center">
                            <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                              v-model="user.permissions[2]" :checked="user.permissions[2]" />
                            <span
                              :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                              class="ml-2 text-xs">Editar</span>
                          </label>
                          <label class="flex items-center">
                            <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                              v-model="user.permissions[3]" :checked="user.permissions[3]" />
                            <span
                              :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                              class="ml-2 text-xs">Eliminar</span>
                          </label>
                          <label class="flex items-center">
                            <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                              v-model="user.permissions[4]" :checked="user.permissions[4]" />
                            <span
                              :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                              class="ml-2 text-xs">Comentar</span>
                          </label>
                        </div>
                        <el-popconfirm v-if="typeAccessProject === 'Private'" confirm-button-text="Si"
                          cancel-button-text="No" icon-color="#D90537" title="Remover?"
                          @confirm="removeUserFromPermissions(user.id)">
                          <template #reference>
                            <button :disabled="user.employee_properties === null" type="button"
                              class="text-primary mr-10 disabled:cursor-not-allowed disabled:opacity-50">
                              <i class="fa-regular fa-circle-xmark"></i>
                            </button>
                          </template>
                        </el-popconfirm>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </section>
          <!-- {{ form }} -->

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing || (editAccesFlag && typeAccessProject == 'Public')">
              Crear oportunidad
            </PrimaryButton>
          </div>
        </div>
      </form>
      <!-- tag form -->
      <DialogModal :show="showTagFormModal" @close="showTagFormModal = false">
        <template #title> Agregar etiqueta </template>
        <template #content>
          <form @submit.prevent="storeTag" ref="tagForm">
            <div>
              <label>Nombre de la etiqueta *</label>
              <input v-model="tagForm.name" type="text" class="input mt-1" placeholder="Escribe el nombre" required />
              <InputError :message="tagForm.errors.name" />
            </div>
            <div class="mt-3">
              <label>Seleccione el color *</label>
              <el-color-picker v-model="tagForm.color" class="mt-1" />
              <InputError :message="tagForm.errors.color" />
            </div>
          </form>
        </template>
        <template #footer>
          <CancelButton @click="showTagFormModal = false" :disabled="tagForm.processing">Cancelar</CancelButton>
          <PrimaryButton @click="submitTagForm()" :disabled="tagForm.processing">Crear</PrimaryButton>
        </template>
      </DialogModal>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import DialogModal from "@/Components/DialogModal.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      seller_id: null,
      status: null,
      description: null,
      company_id: null,
      contact: null,
      tags: [],
      probability: null,
      amount: null,
      priority: null,
      start_date: null,
      estimated_finish_date: null,
      typeAccessProject: "Private",
      lost_oportunity_razon: null,
      media: [],
      selectedUsersToPermissions: [],
      is_new_company: false,
    });

    const tagForm = useForm({
      name: null,
      color: null,
    });

    return {
      form,
      tagForm,
      company_branch: null,
      showTagFormModal: false,
      company_branch_obj: null,
      typeAccessProject: 'Private',
      // owner: this.$page.props.auth.user.name,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      editAccesFlag: true,
      statuses: [
        {
          label: "Nueva",
          color: "text-[#9A9A9A]",
        },
        {
          label: "Pendiente",
          color: "text-[#F3FD85]",
        },
        {
          label: "Cerrada",
          color: "text-[#FEDBBD]",
        },
        {
          label: "Pagado",
          color: "text-[#AFFDB2]",
        },
        {
          label: "Perdida",
          color: "text-[#F7B7FC]",
        },
      ],
      priorities: [
        {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
        {
          label: "Media",
          color: "text-[#D97705]",
        },
        {
          label: "Alta",
          color: "text-[#D90537]",
        },
      ],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    Link,
    InputError,
    IconInput,
    Checkbox,
    ThirthButton,
    RichText,
    FileUploader,
    DialogModal,
    CancelButton,
    Tag,
  },
  props: {
    companies: Array,
    users: Array,
    tags: Object,
  },
  methods: {
    store() {
      this.form.post(route("oportunities.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado una nueva oportunidad",
            type: "success",
          });
        },
      });
    },
    async storeTag() {
      try {
        this.tagForm.processing = true;
        const response = await axios.post(route("tags.store"), {
          name: this.tagForm.name,
          color: this.tagForm.color,
          type: "crm",
          user_id: this.$page.props.auth.user.id,
        });

        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: response.data.message,
            type: "success",
          });

          this.showTagFormModal = false;
          this.tags?.data.push(response.data.item);
          this.tagForm.reset();
          this.tagForm.errors = {};
          this.form.tags.push(response.data.item.id);
        }
      } catch (error) {
        if (error.response.status === 422) {
          // guardando errores de validacion a formulario para mostrarlos
          this.tagForm.errors.name = error.response.data.errors.name[0];
          this.tagForm.errors.color = error.response.data.errors.color[0];
        }
        console.log(error);
      } finally {
        this.tagForm.processing = false;
      }
    },
    submitTagForm() {
      this.$refs.tagForm.dispatchEvent(new Event("submit", { cancelable: true }));
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
    getColorStatus(oportunityStatus) {
      if (oportunityStatus === "Nueva") {
        return "text-[#9A9A9A]";
      } else if (oportunityStatus === "Pendiente") {
        return "text-[#F3FD85]";
      } else if (oportunityStatus === "Cerrada") {
        return "text-[#FEDBBD]";
      } else if (oportunityStatus === "Pagado") {
        return "text-[#AFFDB2]";
      } else if (oportunityStatus === "Perdida") {
        return "text-[#F7B7FC]";
      } else {
        return "text-transparent";
      }
    },
    getColorPriority(oportunityPriority) {
      if (oportunityPriority === "Baja") {
        return "text-[#87CEEB]";
      } else if (oportunityPriority === "Media") {
        return "text-[#D97705]";
      } else if (oportunityPriority === "Alta") {
        return "text-[#D90537]";
      } else {
        return "text-transparent";
      }
    },
    handleChecked() {
      //resetea la busqueda de contacto en formulario
      this.form.company_id = null;
      this.company_branch_obj = null;
      this.company_branch = null;
      this.form.contact = null;
    },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find(
        (item) => item.id == this.form.company_id
      )?.company_branches[0];
      // console.log(this.company_branch_obj);
    },
    updateDescription(content) {
      this.form.description = content;
    },
    selectAdmins() {
      // obtener los usuarios admin para que siempre aparezcan en los proyectos y dar todos los permisos
      let admins = this.users.filter(item => item.employee_properties == null);
      admins.forEach(admin => {
        const defaultPermissions = [true, true, true, true, true];
        admin.permissions = defaultPermissions;
      });
      this.form.selectedUsersToPermissions = admins;
    },
    selectAuthUser() {
      // obtener usuario que esta creando el proyecto para dar todos los permisos
      const user = this.users.find(item => item.id === this.$page.props.auth.user.id);
      const defaultPermissions = [true, true, true, true, true];
      let authUser = {
        id: user.id,
        name: user.name,
        profile_photo_url: user.profile_photo_url,
        permissions: [...defaultPermissions],
      };
      this.form.selectedUsersToPermissions.push(authUser);
    },
    removeUserFromPermissions(userId) {
      const index = this.form.selectedUsersToPermissions.findIndex(item => item.id === userId);

      this.form.selectedUsersToPermissions.splice(index, 1);
    },
    addToSelectedUsers(userId) {
      const user = this.users.find(item => item.id === userId);
      const defaultPermissions = [false, true, false, false, true];
      let foundUser = {
        id: user.id,
        name: user.name,
        profile_photo_url: user.profile_photo_url,
        permissions: [...defaultPermissions],
      };
      this.form.selectedUsersToPermissions.push(foundUser);
    },
  },
  computed: {
    availableUsersToPermissions() {
      if (this.form.selectedUsersToPermissions.length == 0) return this.users;

      const availableUsers = this.users.filter((item) => {
        // Verifica si el item no se encuentra en item2
        return !this.form.selectedUsersToPermissions.find((item2) => item.id === item2.id);
      });

      return availableUsers;
    }
  },
  watch: {
    typeAccessProject(newVal) {
      this.selectAdmins();
      if (newVal === 'Public') {
        let defaultPermissions = [false, true, false, false, true];
        let usersWithSelectedProperties = this.users.filter(element => element.employee_properties !== null).map(user => ({
          id: user.id,
          name: user.name,
          profile_photo_url: user.profile_photo_url,
          permissions: [...defaultPermissions],
        }));
        this.form.selectedUsersToPermissions = [...this.form.selectedUsersToPermissions, ...usersWithSelectedProperties];
        this.editAccesFlag = false;
      } else {
        this.editAccesFlag = true;
      }
    }
  },
  mounted() {
    this.selectAdmins();
    this.selectAuthUser();
  }
};
</script>
