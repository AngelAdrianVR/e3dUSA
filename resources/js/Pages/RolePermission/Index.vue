<template>
  <div>
    <AppLayoutNoHeader title="Roles y permisos">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Roles y permisos</span>
      </div>
      <div class="flex justify-end mt-5 mx-14">
        <PrimaryButton v-if="$page.props.auth.user.permissions.includes('Crear roles y permisos')"
          @click="tabs == 1 ? createRole() : createPermission()" class="h-9 rounded-lg">
          Crear
        </PrimaryButton>
      </div>
      <div class="mt-7 border-b-2">
        <!-- ------------------------Information panel tabs--------------------- -->
        <div class="border-2">
          <div class="border-b-2 px-7 py-3 flex">
            <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base leading-none">
              Roles
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
              Permisos
            </p>
          </div>
          <!-- --------------------- Tab 1 roles starts------------------ -->
          <div v-if="tabs == 1" class="px-7 py-7 text-sm">
            <table class="w-full">
              <thead>
                <tr class="text-left border-b border-primary *:pb-2 *:font-bold">
                  <th class="font-normal">#</th>
                  <th class="font-normal">Roles</th>
                  <th class="font-normal">Fecha de creación</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(role, index) in roles.data" :key="role.id" class="mb-4">
                  <td class="text-left pt-3">
                    {{ role.id }}
                  </td>
                  <td @click="editRole(role, index)" class="text-left pt-3">
                    <span class="hover:underline cursor-pointer">{{ role.name }}</span>
                  </td>
                  <td class="text-left pt-3">
                    {{ role.created_at }}
                  </td>
                  <td class="text-left pt-3">
                    <div>
                      <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar roles y permisos')"
                        confirm-button-text="Si" cancel-button-text="No" icon-color="#FFFFFF" title="¿Continuar?"
                        @confirm="deleteRole(role, index)">
                        <template #reference>
                          <i class="fa-regular fa-trash-can hover:text-red-600 p-2 cursor-pointer"></i>
                        </template>
                      </el-popconfirm>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- --------------------- Tab 1 roles ends------------------ -->

          <!-- --------------------- Tab 2 permissions starts------------------ -->
          <div v-if="tabs == 2" class="p-2 text-sm overflow-scroll">
            <div class="lg:grid grid-cols-4">
              <div v-for="(guard, index) in Object.keys(permissions.data)" :key="index" class="border border-gray2 p-2">
                <h1 class="text-secondary bg-gray2 p-2">{{ guard.replace(/_/g, " ") }}</h1>
                <div v-for="(permission, index2) in permissions.data[guard]" :key="index"
                  class="flex justify-between items-center mt-1">
                  <p @click="editPermission(permission, index2)" class="hover:underline cursor-pointer">{{ permission.name
                  }}</p>
                  <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar roles y permisos')"
                    confirm-button-text="Si" cancel-button-text="No" icon-color="#FFFFFF" title="¿Continuar?"
                    @confirm="deletePermission(permission, index2)">
                    <template #reference>
                      <i class="fa-regular fa-trash-can hover:text-red-600 p-1 cursor-pointer"></i>
                    </template>
                  </el-popconfirm>
                </div>
              </div>
            </div>
          </div>
          <!-- --------------------- Tab 2 permissions ends------------------ -->

        </div>
      </div>

      <!-- Role modal -->
      <DialogModal :show="showRoleModal" @close="showRoleModal = false">
        <template #title>
          <p v-if="editFlag">Rol {{ currentRole.name }}</p>
          <p v-else>Crear nuevo rol</p>
        </template>
        <template #content>
          <div>
            <form @submit.prevent="editFlag ? updateRole() : storeRole()" ref="myRoleForm" class="grid grid-cols-3">
              <div class="col-span-full">
                <IconInput v-if="!editFlag" v-model="roleForm.name" inputPlaceholder="Nombre de rol *" inputType="text">
                  <el-tooltip content="Nombre de rol *" placement="top">
                    A
                  </el-tooltip>
                </IconInput>
                <InputError :message="roleForm.errors.name" />
              </div>
              <p class="text-secondary mb-2 col-span-full">Asignar permisos</p>
              <div v-for="(guard, index) in Object.keys(permissions.data)" :key="index" class="border p-3">
                <h1 class="text-secondary">{{ guard.replace(/_/g, " ") }}</h1>
                <label v-for="permission in permissions.data[guard]" :key="permission.id" class="flex items-center">
                  <input type="checkbox" v-model="roleForm.permissions" :value="permission.id"
                    class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                  <span class="ml-2 text-sm">{{ permission.name }}</span>
                </label>
              </div>
            </form>
          </div>
        </template>
        <template #footer>
          <CancelButton @click="showRoleModal = false; roleForm.reset(); editFlag = false;"
            :disabled="roleForm.processing">Cancelar</CancelButton>
          <PrimaryButton @click="submitRoleForm" :disabled="roleForm.processing">{{ editFlag ? 'Actualizar' : 'Crear' }}
          </PrimaryButton>
        </template>
      </DialogModal>

      <!-- Permission modal -->
      <DialogModal :show="showPermissionModal" @close="showPermissionModal = false">
        <template #title>
          <p v-if="editFlag">Editar permiso</p>
          <p v-else>Crear nuevo permiso</p>
        </template>
        <template #content>
          <div>
            <form @submit.prevent="editFlag ? updatePermission() : storePermission()" ref="myPermissionForm">
              <div>
                <IconInput v-model="permissionForm.name" inputPlaceholder="Nombre del permiso *" inputType="text">
                  <el-tooltip content="Nombre del permiso *" placement="top">
                    A
                  </el-tooltip>
                </IconInput>
                <InputError :message="permissionForm.errors.name" />
              </div>
              <div @keyup.enter="submitPermissionForm" class="mt-3">
                <IconInput v-model="permissionForm.category" inputPlaceholder="Categoria del permiso *" inputType="text">
                  <el-tooltip content="Categoria del permiso *" placement="top">
                    A
                  </el-tooltip>
                </IconInput>
                <InputError :message="permissionForm.errors.category" />
              </div>
            </form>
          </div>
        </template>
        <template #footer>
          <CancelButton @click="showPermissionModal = false; permissionForm.reset(); editFlag = false;"
            :disabled="permissionForm.processing">Cancelar</CancelButton>
          <PrimaryButton @click="submitPermissionForm" :disabled="permissionForm.processing">
            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
            {{ editFlag ? 'Actualizar' : 'Crear' }}
          </PrimaryButton>
        </template>
      </DialogModal>
    </AppLayoutNoHeader>
  </div>
</template>
  
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";

export default {
  data() {
    const roleForm = useForm({
      name: null,
      permissions: [],
    });

    const permissionForm = useForm({
      name: null,
      category: null,
    });

    return {
      roleForm,
      permissionForm,
      currentRole: null,
      currentPermission: null,
      showRoleModal: false,
      showPermissionModal: false,
      indexRoleEdit: null,
      indexPermissionEdit: null,
      editFlag: false,
      tabs: 1,
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    CancelButton,
    DialogModal,
    IconInput,
    InputError
  },
  props: {
    roles: Object,
    permissions: Object,
  },
  methods: {
    // roles
    async deleteRole(role, index) {
      try {
        const response = await axios.delete(route('role-permission.delete-role', role));

        if (response.status === 200) {
          this.roles.data.splice(index, 1);
          this.$notify({
            title: 'Éxito',
            message: response.data.message,
            type: 'success'
          });
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    editRole(role, index) {
      if (this.$page.props.auth.user.permissions.includes('Editar roles y permisos')) {
        this.currentRole = role;
        this.editFlag = true;
        this.indexRoleEdit = index;
        this.showRoleModal = true;

        this.roleForm.name = role.name;
        this.roleForm.permissions = role.permissions.ids;
      }
    },
    createRole() {
      this.currentRole = null;
      this.showRoleModal = true;
      this.editFlag = false;
      this.roleForm.reset();
    },
    async updateRole() {
      try {
        const response = await axios.put(route('role-permission.update-role', this.currentRole), {
          name: this.roleForm.name,
          permissions: this.roleForm.permissions,
        });

        if (response.status === 200) {
          this.$notify({
            title: 'Éxito',
            message: 'Rol actualizado',
            type: 'success'
          });
          this.roles.data[this.indexRoleEdit] = response.data.item;
          this.roleForm.reset();
          this.showRoleModal = false;
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    async storeRole() {
      try {
        const response = await axios.post(route('role-permission.store-role'), {
          name: this.roleForm.name,
          permissions: this.roleForm.permissions,
        });

        if (response.status === 200) {
          this.$notify({
            title: 'Éxito',
            message: 'Rol creado',
            type: 'success'
          });
          this.roles.data.push(response.data.item);
          this.roleForm.reset();
          this.showRoleModal = false;
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    submitRoleForm() {
      this.$refs.myRoleForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },

    // permissions
    async deletePermission(permission, index) {
      try {
        const response = await axios.delete(route('role-permission.delete-permission', permission));

        if (response.status === 200) {
          this.permissions.data[permission.category].splice(index, 1);
          this.$notify({
            title: 'Éxito',
            message: response.data.message,
            type: 'success'
          });
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    editPermission(permission, index) {
      if (this.$page.props.auth.user.permissions.includes('Editar roles y permisos')) {
        this.currentPermission = permission;
        this.editFlag = true;
        this.indexPermissionEdit = index;
        this.showPermissionModal = true;

        this.permissionForm.name = permission.name;
        this.permissionForm.category = permission.category;
      }
    },
    createPermission() {
      this.currentPermission = null;
      this.showPermissionModal = true;
      this.editFlag = false;
    },
    async updatePermission() {
      try {
        const response = await axios.put(route('role-permission.update-permission', this.currentPermission), {
          name: this.permissionForm.name,
          category: this.permissionForm.category,
        });

        if (response.status === 200) {
          this.$notify({
            title: 'Éxito',
            message: 'Permiso actualizado',
            type: 'success'
          });
          this.permissions.data[response.data.item.category][this.indexPermissionEdit] = response.data.item;
          this.permissionForm.reset();
          this.showPermissionModal = false;
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    async storePermission() {
      try {
        const response = await axios.post(route('role-permission.store-permission'), {
          name: this.permissionForm.name,
          category: this.permissionForm.category,
        });

        if (response.status === 200) {
          this.$notify({
            title: 'Éxito',
            message: 'Permiso creado',
            type: 'success'
          });
          this.permissions.data[response.data.item.category].push(response.data.item);
          this.permissionForm.reset();
          this.showPermissionModal = false;
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    submitPermissionForm() {
      this.$refs.myPermissionForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
  }
};
</script>
  