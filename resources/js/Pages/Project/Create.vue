<template>
  <div>
    <AppLayout title="Crear proyecto">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('projects.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear proyecto</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
          <div>
            <label>Título del proyecto *</label>
            <input v-model="form.project_name" class="input" type="text" placeholder="Escribe el título">
            <InputError :message="form.errors.project_name" />
          </div>
          <div>
            <label>Responsable *</label>
            <el-select v-model="form.owner_id" clearable placeholder="Seleccione" class="w-full mt-1"
              no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in users" :key="item.id" :label="item.name" :value="item.id">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                  class="flex text-sm rounded-full items-center mt-[3px]">
                  <img class="h-7 w-7 rounded-full object-cover mr-4" :src="item.profile_photo_url" :alt="item.name" />
                  <p>{{ item.name }}</p>
                </div>
              </el-option>
            </el-select>
            <InputError :message="form.errors.owner_id" />
          </div>
          <div class="lg:flex pt-3">
            <div class="lg:w-1/2 mt-2 lg:mt-0">
              <label class="block">Fecha de inicio *</label>
              <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha de inicio *" format="YYYY/MM/DD"
                value-format="YYYY-MM-DD" />
              <InputError :message="form.errors.start_date" />
            </div>
            <div class="lg:w-1/2 mt-2 lg:mt-0">
              <label class="block">Fecha de final *</label>
              <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha de final *" format="YYYY/MM/DD"
                value-format="YYYY-MM-DD" />
              <InputError :message="form.errors.limit_date" />
            </div>
          </div>
          <div class="flex justify-end items-center space-x-3 mr-auto mt-2">
            <label class="flex items-center">
              <Checkbox v-model:checked="form.is_strict_project" class="bg-transparent" />
              <span class="ml-2 text-sm">Proyecto estricto</span>
            </label>
            <el-tooltip
              content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto "
              placement="top">
              <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                <i class="fa-solid fa-info text-primary text-[7px]"></i>
              </div>
            </el-tooltip>
          </div>
          <div class="mt-5 col-span-full">
            <label>Descripción</label>
            <RichText @content="updateDescription($event)" />
          </div>
          <div class="ml-2 mt-2 col-span-full flex">
            <FileUploader @files-selected="this.form.media = $event" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
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
            <div>
              <div class="flex justify-between items-center mx-2">
                <div class="flex items-center space-x-2">
                  <label>Grupo</label>
                  <el-tooltip
                    content="Organice su proyecto en grupos. Seleccione o cree un grupo para asociar este proyecto"
                    placement="right">
                    <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </div>
                <button @click="showGroupFormModal = true" type="button" class="text-primary text-xs">
                  Agregar grupo nuevo
                </button>
              </div>
              <el-select v-model="form.project_group_id" clearable placeholder="Seleccione" class="w-full mt-1"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="(item, index) in project_groups.data" :key="item.id" :label="item.name"
                  :value="item.id" />
              </el-select>
              <InputError :message="form.errors.project_group_id" />
            </div>
          </div>
          <div class="flex items-center cursor-pointer flex-shrink-0 flex-grow-0">
            <label class="flex items-center">
              <Checkbox v-model:checked="form.is_internal_project" class="bg-transparent" />
              <span class="ml-2 text-sm text-black">Proyecto interno</span>
            </label>
            <el-tooltip class="ml-2"
              content="Seleccione esta opción si el proyecto es una iniciativa de la empresa y no esta relacionado con un cliente en específico"
              placement="right">
              <!-- <i class="fa-solid fa-circle-info text-primary text-xs ml-2"></i> -->
              <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
                <i class="fa-solid fa-info text-primary text-[7px]"></i>
              </div>
            </el-tooltip>
          </div>

          <section v-if="!form.is_internal_project">
            <h2 class="font-bold my-3">Campos adicionales</h2>

            <div class="flex space-x-7 justify-between">
              <div class="w-1/2">
                <label>Cliente *</label> <br>
                <el-select v-model="form.company_id" clearable filterable placeholder="Seleccione"
                  no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
                  <el-option v-for="company in companies" :key="company" :label="company.business_name"
                    :value="company.id" />
                </el-select>
                <InputError :message="form.errors.company_id" />
              </div>
              <div class="w-1/2">
                <label>Sucursal *</label> <br>
                <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch_id" clearable filterable
                  placeholder="Seleccione" no-data-text="No hay sucursales registradas"
                  no-match-text="No se encontraron coincidencias">
                  <el-option
                    v-for="company_branch in companies.find((item) => item.id == form.company_id)?.company_branches"
                    :key="company_branch" :label="company_branch.name" :value="company_branch.id" />
                </el-select>
                <InputError :message="form.errors.company_branch" />
              </div>
            </div>

            <div class="flex space-x-7 justify-between my-2">
              <div class="w-1/2">
                <label>Dirección <span @click="form.shipping_address = company_branch_obj?.address"
                    class="text-primary text-xs cursor-pointer ml-4">Dirección de sucursal</span></label> <br>
                <input v-model="form.shipping_address" class="input text-gray-600" type="text">
                <InputError :message="form.errors.shipping_address" />
              </div>
              <div class="w-1/2">
                <label>OV *</label> <br>
                <el-select v-model="form.sale_id" clearable filterable placeholder="Seleccione"
                  no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
                  <el-option v-for="ov in company_branch_obj?.sales" :key="ov?.id" :label="'OV-0' + ov?.id"
                    :value="ov?.id" />
                </el-select>
                <InputError :message="form.errors.sale_id" />
              </div>
            </div>
          </section>

          <h2 class="font-bold">Presupuesto</h2>

          <div class="flex space-x-7 justify-between">
            <div class="w-1/2">
              <label>Moneda</label> <br>
              <el-select v-model="form.currency" clearable filterable placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="currency in currencies" :key="currency" :label="currency" :value="currency" />
              </el-select>
              <InputError :message="form.errors.currency" />
            </div>
            <div class="w-1/2">
              <label>Monto</label> <br>
              <input v-model="form.budget" class="input" type="number" min="0">
              <InputError :message="form.errors.budget" />
            </div>
          </div>

          <div class="w-1/2">
            <label>Método de facturación</label>
            <el-select v-model="form.sat_method" clearable filterable placeholder="seleccione"
              no-data-text="No hay metodos registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="sat_method in sat_methods" :key="sat_method" :label="sat_method" :value="sat_method" />
            </el-select>
            <InputError :message="form.errors.sat_method" />
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
                              Crea tareas
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
          <!-- {{form}} -->

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing || (editAccesFlag && typeAccessProject == 'Public')">
              Crear proyecto
            </PrimaryButton>
          </div>
        </div>
      </form>

      <!-- group form -->
      <DialogModal :show="showGroupFormModal" @close="showGroupFormModal = false">
        <template #title>
          Agregar grupo
        </template>
        <template #content>
          <form @submit.prevent="storeGroup" ref="groupForm">
            <div>
              <label>Nombre del grupo *</label>
              <input v-model="groupForm.name" type="text" class="input mt-1" placeholder="Escribe el nombre" required>
              <InputError :message="groupForm.errors.name" />
            </div>
          </form>
        </template>
        <template #footer>
          <CancelButton @click="showGroupFormModal = false" :disabled="groupForm.processing">Cancelar</CancelButton>
          <PrimaryButton @click="submitGroupForm()" :disabled="groupForm.processing">Crear</PrimaryButton>
        </template>
      </DialogModal>

      <!-- tag form -->
      <DialogModal :show="showTagFormModal" @close="showTagFormModal = false">
        <template #title>
          Agregar etiqueta
        </template>
        <template #content>
          <form @submit.prevent="storeTag" ref="tagForm">
            <div>
              <label>Nombre de la etiqueta *</label>
              <input v-model="tagForm.name" type="text" class="input mt-1" placeholder="Escribe el nombre" required>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import DialogModal from "@/Components/DialogModal.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Tag from "@/Components/MyComponents/Tag.vue";

export default {
  data() {
    const form = useForm({
      project_name: null,
      start_date: null,
      limit_date: null,
      is_strict_project: false,
      description: null,
      tags: [],
      project_group_id: null,
      is_internal_project: false,
      company_id: null,
      company_branch_id: null,
      shipping_address: null,
      sale_id: null,
      currency: null,
      budget: null,
      sat_method: null,
      owner_id: this.$page.props.auth.user.id,
      selectedUsersToPermissions: [],
      media: [],
    });

    const groupForm = useForm({
      name: null,
    });

    const tagForm = useForm({
      name: null,
      color: null,
    });

    return {
      form,
      groupForm,
      tagForm,
      editAccesFlag: true,
      showGroupFormModal: false,
      showTagFormModal: false,
      company_branch_obj: null,
      typeAccessProject: 'Private',
      currencies: [
        'MXN - Peso Mexicano',
        'USD - Dolar',
      ],
      sat_methods: [
        'Facturación al contado',
        'Facturación a crédito',
        'Facturación por adelantado',
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
    Tag,
    DialogModal,
    CancelButton,
  },
  props: {
    companies: Array,
    users: Array,
    tags: Object,
    project_groups: Object,
  },
  methods: {
    submitGroupForm() {
      this.$refs.groupForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    async storeGroup() {
      try {
        this.groupForm.processing = true;
        const response = await axios.post(route('project-groups.store'), { name: this.groupForm.name, user_id: this.$page.props.auth.user.id });

        if (response.status === 200) {
          this.$notify({
            title: 'Correcto',
            message: response.data.message,
            type: 'success'
          });

          this.showGroupFormModal = false;
          this.project_groups.data.push(response.data.item);
          this.groupForm.reset();
          this.groupForm.errors = {};
          this.form.project_group_id = response.data.item.id;
        }
      } catch (error) {
        if (error.response.status === 422) {
          // guardando errores de validacion a formulario para mostrarlos
          this.groupForm.errors.name = error.response.data.errors.name[0];
        }
        console.log(error)
      } finally {
        this.groupForm.processing = false;
      }
    },
    submitTagForm() {
      this.$refs.tagForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    async storeTag() {
      try {
        this.tagForm.processing = true;
        const response = await axios.post(route('tags.store'), { name: this.tagForm.name, color: this.tagForm.color, type: 'projects', user_id: this.$page.props.auth.user.id });

        if (response.status === 200) {
          this.$notify({
            title: 'Correcto',
            message: response.data.message,
            type: 'success'
          });

          this.showTagFormModal = false;
          this.tags.data.push(response.data.item);
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
        console.log(error)
      } finally {
        this.tagForm.processing = false;
      }
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
    updateDescription(content) {
      this.form.description = content;
    },
    store() {
      this.form.post(route("projects.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo proyecto",
            type: "success",
          });

        },
      });
    },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
      // console.log(this.company_branch_obj);
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
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

<style scoped>
/* Estilo para el hover de las opciones */
.el-select-dropdown .el-select-dropdown__item:hover {
  background-color: #D90537;
  /* Color de fondo al hacer hover */
  color: white;
  /* Color del texto al hacer hover */
  border-radius: 20px;
  /* Redondeo */
}
</style>
