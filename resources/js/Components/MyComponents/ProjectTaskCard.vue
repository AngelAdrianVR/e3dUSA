<template>
  <div @click="
    taskInformationModal = true;
  itemToShow = taskComponentLocal;
  " :class="taskComponentLocal?.priority.color_border"
    class="shadow-md shadow-gray-400/100 border border-t-[#d9d9d9] border-r-[#d9d9d9] border-b-[#d9d9d9] h-36 rounded-r-md border-l-4 py-2 px-3 cursor-pointer my-3">
    <!-- ------------ top ------------------ -->
    <!-- <el-tooltip :content="'Prioridad: ' + taskComponentLocal?.priority.label" placement="top"> -->
    <div class="flex justify-between items-center">
      <div @click.stop="" class="rounded-full px-2 cursor-move">
        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
      </div>
      <div @click.stop="" class="flex cursor-default">
        <p v-if="taskComponentLocal?.is_paused" class="mr-4 rounded-full text-orange-500 bg-orange-200 px-2">
          {{ "Pausado" }}
        </p>
        <p class="mr-5">{{ taskComponentLocal?.created_at }}</p>
      </div>
    </div>
    <!-- </el-tooltip> -->
    <!-- ------------ body -------------------------- -->
    <div class="flex items-center justify-between p-3">
      <p class="text-sm">{{ taskComponentLocal?.title }}</p>
      <div>
        <!-- <el-tooltip content="Tienes una tarea por cumplir antes de poder comenzar" placement="top">
                            <i @click.stop="" class="fa-solid fa-hourglass cursor-default mr-3"></i>
                        </el-tooltip> -->
        <el-tooltip v-if="taskComponentLocal?.media.length" content="Archivos adjunto" placement="top">
          <i @click.stop="" class="fa-solid fa-paperclip rounded-full p-2"></i>
        </el-tooltip>
      </div>
    </div>

    <!-- ----------------- footer --------------- -->
    <footer class="p-3 border-t border-[#9A9A9A] relative">
      <div class="flex justify-between items-center px-3">
        <div class="flex items-center text-[#9A9A9A]">
          <i class="fa-regular fa-comments text-lg rounded-full py-1 px-2"></i>
          <p class="text-xs">{{ taskComponentLocal?.comments?.length }}</p>
          <p class="text-sm ml-1">| {{ "Dpto. " + taskComponentLocal?.department }}</p>
        </div>
        <div class="flex items-center absolute bottom-3 right-0 cursor-default">
          <el-tooltip v-if="taskComponentLocal?.status == 'Terminada'" content="Tarea terminada" placement="bottom">
            <i @click.stop="" class="fa-solid fa-check text-green-500 text-xl cursor-default mr-2"></i>
          </el-tooltip>
          <el-tooltip v-if="taskComponentLocal?.participants?.length > 2" placement="top">
            <p class="text-primary mr-1"> + {{ taskComponentLocal?.participants.length - 2 }}</p>
            <template #content>
              <div>
                <p v-for="user in taskComponentLocal?.participants.slice(2, taskComponentLocal?.participants.length)"
                  :key="user">{{ user.name }}</p>
              </div>
            </template>
          </el-tooltip>
          <el-tooltip v-for="user in taskComponentLocal?.participants.slice(0, 2)" :key="user" :content="user.name"
            placement="bottom">
            <figure @click.stop="">
              <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full">
                <img class="h-9 w-9 rounded-full object-cover" :src="user.profile_photo_url" :alt="user.name" />
              </div>
            </figure>
          </el-tooltip>
        </div>
      </div>
    </footer>
  </div>

  <!-- -------------- task information Modal -------------- -->
  <Modal :show="taskInformationModal" @close="taskInformationModal = false">
    <div class="mx-7 my-4 space-y-4 relative">
      <div @click="taskInformationModal = false"
        class="cursor-pointer w-5 h-5 rounded-full flex items-center justify-center absolute top-0 right-0">
        <i class="fa-solid fa-xmark"></i>
      </div>
      <h1 class="font-bold">{{ taskComponentLocal?.title }}</h1>

      <div class="relative">
        <label>
          Estado actual
          <i :class="getColorStatus(form.status)" class="fa-solid fa-circle text-xs ml-1"></i>
        </label> <br />
        <div class="flex items-center space-x-4">
          <el-select :disabled="taskComponentLocal?.is_paused || !authUserIsParticipant" class="lg:w-1/2"
            v-model="form.status" @change="updateStatus()" clearable filterable placeholder="Seleccionar estatus"
            no-data-text="No hay estatus registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{ item.label }}</span>
            </el-option>
          </el-select>
          <el-tooltip v-if="taskComponentLocal?.status == 'En curso'"
            :content="taskComponentLocal?.is_paused ? 'Reanudar tarea' : 'Pausar tarea'" placement="top">
            <button type="button">
              <i @click.stop="playPauseTask(taskComponentLocal)" :class="taskComponentLocal?.is_paused ? 'fa-circle-play' : 'fa-circle-pause'
                " class="fa-regular text-secondary text-xl cursor-pointer"></i>
            </button>
          </el-tooltip>
        </div>
        <InputError :message="form.errors.status" />
      </div>
      <h2 class="font-bold">Información de la tarea</h2>
      <div>
        <div class="flex space-x-2 justify-between items-center">
          <label>Proyecto</label>
          <input v-model="form.project_name" disabled class="input w-[78%]" type="text" />
          <InputError :message="form.errors.project_name" />
        </div>
        <div class="flex space-x-2 justify-between items-center mt-2">
          <label>Creado por</label>
          <input v-model="form.user" disabled class="input w-[78%]" type="text" />
          <InputError :message="form.errors.user" />
        </div>
        <div class="flex space-x-2 justify-between items-center mt-2">
          <label>Departamento</label>
          <el-select class="!w-[78%]" v-model="form.department" clearable filterable :disabled="!canEdit"
            placeholder="Seleccionar departamento" no-data-text="No hay departamentos registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in departments" :key="item" :label="item" :value="item" />
          </el-select>
          <InputError :message="form.errors.department" />
        </div>
        <div class="flex space-x-2 justify-between items-center mt-2">
          <label>Agregar participantes</label> <br>
          <el-select class="!w-[95%]" v-model="form.participants" clearable filterable multiple :disabled="!canEdit"
            placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id" />
          </el-select>
          <InputError :message="form.errors.participants" />
        </div>
        <div class="mt-2">
          <label>Descripción</label>
          <RichText v-if="canEdit" @content="updateDescription($event)" />
          <div v-else class="rounded-[10px] bg-[#cccccc] px-3 py-2 min-h-[100px] text-sm">{{ form.description }}</div>
          <InputError :message="form.errors.description" />
        </div>
        <div class="mt-3 relative">
          <label>
            Prioridad
            <i :class="getColorPriority(form.priority)" class="fa-solid fa-circle text-xs ml-1"></i>
          </label>
          <el-select class="w-full" v-model="form.priority" clearable filterable placeholder="Seleccionar prioridad"
            :disabled="!canEdit" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
          <InputError :message="form.errors.priority" />
        </div>
        <div class="grid grid-cols-2 gap-4 mt-3">
          <div>
            <label class="block">fecha de inicio</label>
            <el-date-picker v-model="form.start_date" type="date"
              :disabled="authUser.id != taskComponent.user.id && authUser.id != taskComponent.project_owner.id"
              placeholder="Fecha de inicio *" format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.start_date" />
          </div>
          <div>
            <label class="block">fecha final</label>
            <el-date-picker v-model="form.limit_date" type="date"
              :disabled="authUser.id != taskComponent.user.id && authUser.id != taskComponent.project_owner.id"
              placeholder="Fecha límite *" format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.limit_date" />
          </div>
        </div>

        <!-- <div class="w-1/2 mt-3">
          <label>Recordatorio</label>
          <textarea v-model="form.reminder" disabled class="textarea w-full"> </textarea>
          <InputError :message="form.errors.reminder" />
        </div> -->
        <!-- --------------------- TABS -------------------- -->
        <section class="mt-9">
          <div class="flex items-center justify-center">
            <p @click="tabs = 1" :class="tabs == 1 ? 'border-b-2 border-[#D90537] text-primary' : ''"
              class="h-8 p-1 cursor-pointer ml-5 transition duration-300 ease-in-out text-xs md:text-base">
              Comentarios ({{ taskComponentLocal?.comments?.length }})
            </p>
            <div class="border-r-2 border-[#cccccc] h-7 ml-3"></div>
            <p @click="tabs = 2" :class="tabs == 2 ? 'border-b-2 border-[#D90537] text-primary' : ''"
              class="ml-3 h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
              Documentos
            </p>
            <!-- <div class="border-r-2 border-[#cccccc] h-7 ml-3"></div> -->
            <!-- <p @click="tabs = 3" :class="tabs == 3 ? 'border-b-2 border-[#D90537] text-primary' : ''"
              class="ml-3 h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
              Historial
            </p> -->
          </div>
          <!-- -------------- Tab 1 comentarios starts ----------------->
          <div v-if="tabs == 1" class="mt-7 min-h-[170px]">
            <div>
              <figure class="flex space-x-2 mt-4" v-for="comment in taskComponentLocal?.comments" :key="comment">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
                  <img class="h-8 w-8 rounded-full object-cover" :src="comment.user?.profile_photo_url"
                    :alt="comment.user?.name" />
                </div>
                <div class="text-sm w-full">
                  <p class="font-bold">{{ comment.user?.name }}</p>
                  <p v-html="comment.body"></p>
                </div>
              </figure>
              <div v-if="toBool(authUserPermissions[4])" class="flex space-x-1 mt-5">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
                  <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name" />
                </div>
                <RichText @submitComment="storeComment(taskComponentLocal)" @content="updateComment($event)"
                  ref="commentEditor" class="flex-1" withFooter :userList="users" :disabled="sendingComments" />
              </div>
            </div>
          </div>
          <!-- ---------------- tab 1 comentarios ends  -------------->

          <!-- -------------- Tab 2 documentos starts ----------------->
          <div v-if="tabs == 2" class="mt-7 min-h-[170px]">
            <a :href="file?.original_url" target="_blank" v-for="file in taskComponentLocal?.media" :key="file"
              class="flex justify-between items-center cursor-pointer">
              <div class="flex space-x-7 items-center">
                <i :class="getFileTypeIcon(file.file_name)"></i>
                <span class="ml-2">{{ file.file_name }}</span>
              </div>
              <i class="fa-solid fa-download text-right text-sm text-[#9a9a9a]"></i>
            </a>
          </div>
          <!-- ---------------- tab 2 documentos ends  -------------->

          <!-- -------------- Tab 3 historial starts ----------------->
          <div v-if="tabs == 3" class="mt-7 min-h-[170px]"></div>
          <!-- ---------------- tab 3 historial ends  -------------->
        </section>
      </div>
      <!-- {{ form }} -->
      <div class="flex justify-end space-x-3 pt-5 pb-1">
        <CancelButton @click="!canEdit ? taskInformationModal = false : canEdit = false">
          {{ !canEdit ? 'Cancelar' : 'Cancelar edición' }}
        </CancelButton>
        <el-dropdown v-if="toBool(authUserPermissions[2])" split-button type="primary"
          @click="candEdit ? update : canEdit = true" class="custom-dropdown rounded-lg">
          <span v-if="canEdit">Guardar cambios</span>
          <span v-else>Editar</span>
          <template #dropdown>
            <el-dropdown-menu v-if="toBool(authUserPermissions[3])">
              <el-dropdown-item @click="showConfirmModal = true">Eliminar</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
      </div>
    </div>
  </Modal>

  <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
    <template #title> Eliminar tarea </template>
    <template #content> ¿Continuar con la eliminación? </template>
    <template #footer>
      <div class="flex space-x-1">
        <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
        <PrimaryButton @click="deleteProjectTask">Eliminar</PrimaryButton>
      </div>
    </template>
  </ConfirmationModal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import axios from "axios";

export default {
  data() {
    const form = useForm({
      status: this.taskComponent.status,
      title: null,
      project_name: this.taskComponent.project.project_name,
      user: this.taskComponent.user.name,
      department: this.taskComponent.department,
      participants: null,
      description: this.taskComponent.description,
      priority: this.taskComponent.priority.label,
      start_date: this.taskComponent.start_date_raw,
      limit_date: this.taskComponent.end_date_raw,
      reminder: this.taskComponent.reminder,
      comment: null,
    });

    return {
      form,
      showConfirmModal: false,
      sendingComments: false,
      taskComponentLocal: null,
      tabs: 1,
      canEdit: false,
      taskInformationModal: false,
      itemToShow: null,
      statuses: [
        {
          label: "Por hacer",
          color: "text-[#9A9A9A]",
          color_border: "border-[#9A9A9A]",
        },
        {
          label: "En curso",
          color: "text-secondary",
          color_border: "border-[#0355B5]",
        },
        {
          label: "Terminada",
          color: "text-green-500",
          color_border: "border-green-500",
        },
      ],
      priorities: [
        {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
        {
          label: "Media",
          color: "text-orange-500",
        },
        {
          label: "Alta",
          color: "text-red-600",
        },
      ],
      departments: ["Marketing", "Ventas", "Produccion", "Diseño"],
    };
  },
  components: {
    Modal,
    PrimaryButton,
    CancelButton,
    Link,
    InputError,
    RichText,
    ConfirmationModal
  },
  props: {
    taskComponent: Object,
    users: Array,
  },
  events: [
    'updated-status'
  ],
  computed: {
    authUser() {
      return this.users.find(item => item.id == this.$page.props.auth.user?.id);
    },
    authUserIsParticipant() {
      return this.taskComponent.participants?.some(user => user.id === this.authUser?.id);
    },
    authUserPermissions() {
      const permissions = this.authUser?.pivot.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    }
  },
  methods: {
    async updateStatus() {
      try {
        const response = await axios.put(route('tasks.update-status', this.taskComponentLocal.id), { status: this.form.status });

        if (response.status === 200) {
          this.taskComponentLocal.status = this.form.status;
          this.$emit('updated-status', this.taskComponentLocal);
        }
      } catch (error) {
        console.log(error);
      }
    },
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    updateDescription(content) {
      this.form.description = content;
    },
    updateComment(content) {
      this.form.comment = content;
    },
    async playPauseTask(task) {
      try {
        const response = await axios.put(route('tasks.pause-play', task));

        if (response.status === 200) {
          this.taskComponentLocal = response.data.item;

          if (this.taskComponentLocal.is_paused) {
            this.$notify({
              title: "Éxito",
              message: "Se ha pausado la tarea",
              type: "success",
            });
          } else {
            this.$notify({
              title: "Éxito",
              message: "Se ha reanudado la tarea",
              type: "success",
            });
          }
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.taskInformationModal = false;
      }
    },
    async update() {
      try {
        const response = await axios.put(route("tasks.update", this.taskComponentLocal), {
          status: this.form.status,
          department: this.form.department,
          participants: this.form.participants,
          description: this.form.description,
          priority: this.form.priority,
          start_date: this.form.start_date,
          limit_date: this.form.end_date,
          reminder: this.form.reminder,
          comment: this.form.comment,
        });
        if (response.status === 200) {
          this.taskComponentLocal = response.data.item;
          this.$notify({
            title: "Éxito",
            message: "Se ha actualizado la tarea",
            type: "success",
          });
          this.taskComponentLocal = response.data.item;
          this.taskInformationModal = false;
          this.$emit('updated-status', this.taskComponentLocal);
        }
      } catch (error) {
        console.log(error);
      }
    },
    async storeComment() {
      const editor = this.$refs.commentEditor;
      if (this.form.comment) {
        this.sendingComments = true;
        try {
          const response = await axios.post(route("tasks.comment", this.taskComponentLocal.id), {
            comment: this.form.comment,
            mentions: editor.mentions,
          });
          if (response.status === 200) {
            this.taskComponentLocal?.comments.push(response.data.item);
            this.form.comment = null;
            editor.clearContent();
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.sendingComments = false;
        }
      }
    },
    getColorStatus(taskStatus) {
      if (taskStatus === "Por hacer") {
        return "text-[#9A9A9A]";
      } else if (taskStatus === "En curso") {
        return "text-[#0355B5]";
      } else {
        return "text-green-500";
      }
    },
    getColorPriority(taskPriority) {
      if (taskPriority === "Baja") {
        return "text-[#87CEEB]";
      } else if (taskPriority === "Media") {
        return "text-orange-500";
      } else {
        return "text-red-600";
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
    deleteProjectTask() {
      this.$emit('delete-task', this.taskComponent.id);
      this.taskInformationModal = false;
      this.showConfirmModal = false;
    },
  },
  mounted() {
    this.taskComponentLocal = this.taskComponent;
    this.form.participants = this.taskComponent.participants.map(user => user.id);
  },
};
</script>
