<template>
  <div>
    <AppLayoutNoHeader title="Roles y permisos">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Roles y permisos</span>
      </div>
      <div class="flex justify-end mt-5 mx-14">
        <PrimaryButton class="h-9 rounded-lg">
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
                <tr class="text-left">  
                  <th class="font-normal pb-3">#</th>
                  <th class="font-normal">Roles</th>
                  <th class="font-normal">Fecha de creación</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(role, index) in roles.data" :key="role.id" class="text-[#9A9A9A] mb-4">
                  <td @click="editRole(role)" class="text-left pb-3">
                    {{ role.id }}
                  </td>
                  <td @click="editRole(role)" class="text-left pb-3">
                    <span class="hover:underline cursor-pointer">{{ role.name }}</span>
                  </td>
                  <td class="text-left pb-3">
                    {{ role.created_at }}
                  </td>
                  <td class="text-left pb-3">
                    <div>
                      <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FFFFFF"
                        title="¿Continuar?" @confirm="deleteRole(role)">
                        <template #reference>
                          <i class="fa-solid fa-trash-can text-red-600 cursor-pointer"></i>
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
          <div v-if="tabs == 2" class="px-7 py-7 text-sm overflow-scroll">
            <div class="lg:grid grid-cols-4">
              <div v-for="(guard, index) in Object.keys(permissions.data)" :key="index" class="border p-3">
                <h1 class="text-secondary">{{ guard.replace(/_/g, " ") }}</h1>
                <p v-for="permission in permissions.data[guard]" :key="index" class="mt-1">{{ permission.name }}</p>
              </div>
            </div>
          </div>
          <!-- --------------------- Tab 2 permissions ends------------------ -->

        </div>
      </div>

      <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            Hola
        </template>
        <template #content>
            content
        </template>
        <template #footer>
            footer
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

export default {
  data() {
    return {
      currentRole: null,
      currentPermission: null,
      showConfirmModal: false,
      showModal: false,
      tabs: 1,
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    CancelButton,
    DialogModal,
  },
  props: {
    roles: Object,
    permissions: Object,
  },
  methods: {
    deleteRole(role) {
      console.log(role);
    },

    editRole(role) {
      this.currentRole = role;
      this.showModal = true;
    },
  }
};
</script>
  