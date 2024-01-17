<template>
  <div>
    <AppLayout title="Editar proyecto">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar proyecto</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
          <div>
            <label>Título del proyecto *</label>
            <input v-model="form.project_name" class="input" type="text" placeholder="Escribe el título">
            <InputError :message="form.errors.project_name" />
          </div>
          <div>
            <label>Responsable *</label>
            <el-select v-model="form.owner_id" placeholder="Seleccione" class="w-full mt-1"
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
          <div class="pt-1">
            <label class="block">Duración *</label>
            <el-date-picker @change="handleDateRange" v-model="range" type="daterange" range-separator="A"
              start-placeholder="Fecha de inicio" end-placeholder="Fecha límite" value-format="YYYY-MM-DD" />
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
            <RichText @content="updateDescription($event)" :defaultValue="form.description" />
          </div>
          <div class="ml-2 mt-2 col-span-full flex">
            <FileUploader @files-selected="this.form.media = $event" />
          </div>
          <div class="col-span-full">
            <li v-for="file in media" :key="file" class="flex items-center justify-between">
              <a :href="file.original_url" target="_blank" class="flex items-center">
                <i :class="getFileTypeIcon(file.file_name)"></i>
                <span class="ml-2">{{ file.file_name }}</span>
              </a>
            </li>
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
              <el-select v-model="form.tags" placeholder="Seleccione" multiple class="w-full"
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
              <el-select v-model="form.project_group_id" placeholder="Seleccione" class="w-full mt-1"
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
                <el-select v-model="form.company_id" filterable placeholder="Seleccione"
                  no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
                  <el-option v-for="company in companies" :key="company" :label="company.business_name"
                    :value="company.id" />
                </el-select>
                <InputError :message="form.errors.company_id" />
              </div>
              <div class="w-1/2">
                <label>Sucursal *</label> <br>
                <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch_id" filterable
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
                <el-select v-model="form.sale_id" filterable placeholder="Seleccione"
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
              <el-select v-model="form.currency" filterable placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="currency in currencies" :key="currency" :label="currency" :value="currency" />
              </el-select>
              <InputError :message="form.errors.currency" />
            </div>
            <div class="w-1/2">
              <label class="flex items-center">
                <span class="mr-2">Monto *</span>
                <el-tooltip content="Es un estimado de cuanto se gastarán en hacer el proyecto" placement="top">
                  <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                  </div>
                </el-tooltip>
              </label>
              <input v-model="form.budget" class="input" type="number" min="0" step="0.01">
              <InputError :message="form.errors.budget" />
            </div>
          </div>

          <!-- <div class="w-1/2">
            <label>Método de facturación</label>
            <el-select v-model="form.sat_method" filterable placeholder="seleccione"
              no-data-text="No hay metodos registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="sat_method in sat_methods" :key="sat_method" :label="sat_method" :value="sat_method" />
            </el-select>
            <InputError :message="form.errors.sat_method" />
          </div> -->

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
          <section class="rounded-[10px] py-12 mx-1 mt-5 max-h-[580px] col-span-full bg-[#CCCCCC]">
            <div class="flex px-16 mb-8">
              <div v-if="typeAccessProject === 'Private'" class="w-full">
                <h2 class="font-bold text-sm my-2 ml-2 col-span-full">Asignar participantes </h2>
                <el-select @change="addToSelectedUsers" filterable placeholder="Seleccionar usuario" class="w-1/2"
                  no-data-text="No hay más usuarios para añadir" no-match-text="No se encontraron coincidencias">
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
                <div class="pl-3 overflow-y-auto min-h-[100px] max-h-[380px]">
                  <div class="flex mt-2 border-b border-[#9A9A9A]" v-for="user in form.selectedUsersToPermissions"
                    :key="user.id">
                    <div class="w-2/3 flex space-x-2">
                      <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-12">
                        <img class="h-10 w-10 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name" />
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
                            v-model:checked="user.permissions[0]" :checked="user.permissions[0]" />
                          <span
                            :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                            class="ml-2 text-xs">
                            Crea tareas
                          </span>
                        </label>
                        <label class="flex items-center">
                          <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                            v-model:checked="user.permissions[1]" :checked="user.permissions[1]" />
                          <span
                            :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                            class="ml-2 text-xs">Ver</span>
                        </label>
                        <label class="flex items-center">
                          <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                            v-model:checked="user.permissions[2]" :checked="user.permissions[2]" />
                          <span
                            :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                            class="ml-2 text-xs">Editar</span>
                        </label>
                        <label class="flex items-center">
                          <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                            v-model:checked="user.permissions[3]" :checked="user.permissions[3]" />
                          <span
                            :class="!editAccesFlag || user.employee_properties === null ? 'text-gray-500/80 cursor-not-allowed' : ''"
                            class="ml-2 text-xs">Eliminar</span>
                        </label>
                        <label class="flex items-center">
                          <Checkbox :disabled="!editAccesFlag || user.employee_properties === null"
                            v-model:checked="user.permissions[4]" :checked="user.permissions[4]" />
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
                </div>
              </div>
            </div>
          </section>
          <!-- {{form}} -->

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing || (editAccesFlag && typeAccessProject == 'Public')">
              Actualizar proyecto
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
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import DialogModal from "@/Components/DialogModal.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Back from "@/Components/MyComponents/Back.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { isSameDay, parseISO } from "date-fns";

export default {
  data() {
    const form = useForm({
      project_name: this.project.data.project_name,
      start_date: this.project.data.start_date_raw,
      limit_date: this.project.data.limit_date_raw,
      is_strict_project: Boolean(this.project.data.is_strict_project),
      description: this.project.data.description,
      tags: [],
      project_group_id: this.project.data.projectGroup.id,
      is_internal_project: Boolean(this.project.data.is_internal_project),
      company_id: this.project.data.company.id,
      company_branch_id: this.project.data.companyBranch.id,
      shipping_address: this.project.data.shipping_address,
      sale_id: this.project.data.sale.id,
      currency: this.project.data.currency,
      budget: this.project.data.budget,
      sat_method: this.project.data.sat_method,
      owner_id: this.project.data.owner.id,
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
      range: null,
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
    InputError,
    IconInput,
    Checkbox,
    ThirthButton,
    RichText,
    FileUploader,
    DialogModal,
    CancelButton,
    Link,
    Back,
    Tag
  },
  props: {
    companies: Array,
    users: Array,
    tags: Object,
    project_groups: Object,
    project: Object,
    media: Array,
  },
  methods: {
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    handleDateRange(range) {
      this.form.start_date = range[0];
      this.form.limit_date = range[1];

      const date1 = parseISO(range[0]);
      const date2 = parseISO(range[1]);

      // Compara si son del mismo día
      if (isSameDay(date1, date2)) {
        this.canSelectTime = true;
      } else {
        this.canSelectTime = false;
        this.enabledTime = false;
      }

    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
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
        employee_properties: user.employee_properties,
        profile_photo_url: user.profile_photo_url,
        permissions: [...defaultPermissions],
      };
      this.form.selectedUsersToPermissions.push(foundUser);
    },
    updateDescription(content) {
      this.form.description = content;
    },
    update() {
      if (this.form.media.length) {
        this.form.post(route("projects.update-with-media", this.project.data.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se ha editado el proyecto",
              type: "success",
            });
          },
        });
      } else {
        this.form.put(route("projects.update", this.project.data.id), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se ha editado el proyecto",
              type: "success",
            });
          },
        });
      }
    },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
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
          employee_properties: user.employee_properties,
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
    // inicializar tags
    this.form.tags = this.project.data.tags.map(tag => tag.id);

    // inicializar permisos
    this.project.data.users.forEach(user => {
      const permissions = JSON.parse(user.pivot.permissions).map(item => this.toBool(item));
      const participant = {
        id: user.id,
        name: user.name,
        employee_properties: user.employee_properties,
        profile_photo_url: user.profile_photo_url,
        permissions: permissions,
      };
      this.form.selectedUsersToPermissions.push(participant);
    });

    // inicializar fechas en range
    this.range = [this.project.data.start_date_raw, this.project.data.limit_date_raw];
  }
};
</script>
