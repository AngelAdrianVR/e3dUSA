<template>
  <div @click="taskInformationModal = true"
    class="rounded-md shadow-md shadow-gray-400/100 group relative h-[196px] cursor-pointer w-80">
    <el-tooltip :content="'Prioridad: ' + oportunityTask?.priority" placement="top">
      <i :class="getPriorityStyles()" class="fa-solid fa-circle text-[9px] absolute top-3 right-2 p-1"></i>
    </el-tooltip>
    <div class="py-3 px-4">
      <p :class="oportunityTask?.finished_at ? 'line-through' : ''">{{ oportunityTask?.name }}</p>
      <div class="flex justify-between items-center">
        <p class="text-gray-400 mt-3 mb-2">Responsable</p>
        <el-tooltip v-if="oportunityTask?.media?.length" content="Archivos adjuntos" placement="top">
          <div class="flex items-center">
          <i @click.stop="" class="fa-solid fa-paperclip rounded-full p-2"></i>
          <p>({{oportunityTask?.media?.length}})</p>
          </div>
        </el-tooltip>
      </div>
      <figure>
        <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full items-center">
          <img class="h-11 w-11 rounded-full object-cover" :src="oportunityTask?.asigned?.profile_photo_url"
            :alt="oportunityTask?.asigned?.name" />
          <p class="ml-3">{{ oportunityTask?.asigned?.name }}</p>
        </div>
      </figure>
      <div class="flex items-center justify-between mt-3">
        <p :class="oportunityTask?.deadline_status === 'Atrasadas' ? 'bg-[#FEB2C4]' : 'bg-[#F6F89E]'"
          class="rounded-full px-3 text-sm">{{ oportunityTask?.deadline_status !== 'Terminar hoy' ?
            oportunityTask?.limit_date + ', ' + oportunityTask?.time : oportunityTask?.time }}</p>
        <div class="flex items-center space-x-3">
          <i v-if="oportunityTask?.finished_at" class="fa-solid fa-check text-lg text-green-500"></i>
          <p class="text-xs text-gray-400">{{ oportunityTask?.comments?.length }}<i
              class="fa-regular fa-comments text-lg rounded-full ml-2"></i></p>
        </div>
      </div>
    </div>
    <button @click.stop="$emit('task-done', oportunityTask?.id)" v-if="!oportunityTask?.finished_at"
      class="text-sm text-white bg-red-600 w-full py-1 rounded-b-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
      Hecho
    </button>
  </div>
  <Modal :show="taskInformationModal" @close="taskInformationModal = false">
    <form @submit.prevent="update" class="mx-7 my-4 space-y-4 relative">
      <div @click="taskInformationModal = false"
        class="cursor-pointer w-5 h-5 rounded-full flex items-center justify-center absolute top-0 right-0 border border-black">
        <i class="fa-solid fa-xmark"></i>
      </div>
      <h1 class="font-bold">{{ oportunityTask?.name }}</h1>

      <h2 class="font-bold">Información de la actividad</h2>

      <div class="flex justify-between items-center space-x-3 text-sm">
        <label>Oportunidad</label>
        <input v-model="oportunity" disabled class="input w-3/4 cursor-not-allowed" type="text">
      </div>

      <div class="flex justify-between items-center space-x-3 text-sm">
        <label>Creado por</label>
        <input v-model="creator" disabled class="input w-3/4 cursor-not-allowed" type="text">
        <InputError :message="form.errors.name" />
      </div>

      <div class="flex justify-between items-center space-x-3 text-sm">
        <label class="lg:pr-16">Responsable</label>
        <el-select class="w-2/3" v-model="form.asigned_id" clearable filterable placeholder="Seleccionar usuario"
          :disabled="!canEdit" no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias">
          <el-option v-for="user in users" :key="user" :label="user.name" :value="user.id" />
        </el-select>
        <InputError :message="form.errors.asigned_id" />
      </div>

      <div class="lg:flex items-center space-x-4">
        <div class="flex items-center space-x-2">
          <label class="lg:mr-16 text-sm">Fecha limite</label>
          <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha limite *" :disabled-date="disabledDate"
            :disabled="!canEdit" format="YYYY-MM-DD" />
          <InputError :message="form.errors.limit_date" />
        </div>
        <div class="flex items-center space-x-2">
          <label class="text-sm">Hora</label>
          <el-time-select v-model="form.time" start="07:00" step="00:15" end="23:30"
              placeholder="Seleccione una hora" :disabled="!canEdit" />
          <InputError :message="form.errors.time" />
        </div>
      </div>

      <div class="flex justify-between items-center space-x-3 text-sm relative">
        <label class="lg:mr-20">Prioridad</label>
        <el-select class="w-full mt-2" v-model="form.priority" clearable filterable placeholder="Seleccionar prioridad"
          :disabled="!canEdit" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
            <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
            <span style="float: center; margin-left: 5px; font-size: 13px">{{
              item.label
            }}</span>
          </el-option>
        </el-select>
        <i :class="getColorPriority(form.priority)"
          class="fa-solid fa-circle text-xs top-3 left-12 lg:left-16 absolute z-30"></i>
        <InputError :message="form.errors.priority" />
      </div>
      <!-- <div class="flex justify-between items-center space-x-2">
        <label class="text-sm">Recordatorio</label>
        <textarea v-model="form.reminder" disabled class="textarea w-3/4 cursor-not-allowed"> </textarea>
        <InputError :message="form.errors.reminder" />
      </div> -->
      <div class="flex justify-between items-center space-x-2">
        <label class="text-sm">Descripción</label>
      <RichText v-if="canEdit" @content="updateDescription($event)" :defaultValue="form.description" />
        <div v-else class="rounded-[10px] bg-[#cccccc] px-3 py-2 min-h-[100px] text-sm w-3/4">{{ form.description }}</div>
        <InputError :message="form.errors.description" />
      </div>

      <div v-if="oportunityTask?.media?.length" class="text-sm">
        <h2 class="font-bold py-5">Archivos adjuntos</h2>
        <a :href="file?.original_url" target="_blank" v-for="file in oportunityTask?.media" :key="file"
          class="flex justify-between items-center cursor-pointer">
          <div class="flex space-x-7 items-center">
            <i :class="getFileTypeIcon(file.file_name)"></i>
            <span class="ml-2">{{ file.file_name }}</span>
          </div>
          <i class="fa-solid fa-download text-right text-sm text-[#9a9a9a]"></i>
        </a>
      </div>


      <h2 class="font-bold py-5">Comentarios</h2>

      <!-- ------- Comments ------- -->
      <div class="mt-7">
        <section v-if="oportunityTask?.comments?.length > 0">
          <figure class="flex space-x-2 mt-4" v-for="comment in oportunityTask?.comments" :key="comment">
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
              <img class="h-8 w-8 rounded-full object-cover" :src="comment.user?.profile_photo_url"
                :alt="comment.user?.name" />
            </div>
            <div class="text-sm w-full">
              <p class="font-bold">{{ comment.user?.name }}</p>
              <p v-html="comment.body"></p>
            </div>
          </figure>
        </section>
        <p class="text-sm text-center text-gray-600" v-else>No hay comentarios</p>
        <div v-if="toBool(authUserPermissions[4]) && oportunityTask?.finished_at == null" class="flex space-x-1 mt-5">
          <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
              :alt="$page.props.auth.user.name" />
          </div>
          <RichText @submitComment="storeComment(taskComponentLocal)" @content="updateComment($event)" ref="commentEditor"
            class="flex-1" withFooter :userList="users" :disabled="sendingComments" />
        </div>
      </div>

      <div class="flex justify-start space-x-3 pt-5 pb-1">
        <el-dropdown v-if="taskComponentLocal.finished_at === null" split-button type="primary" @click="handleClick" class="custom-dropdown" :disabled="(!toBool(authUserPermissions[2]) && !authUserIsParticipant) || canEdit">
          Marcar como hecho
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item v-if="toBool(authUserPermissions[2])" @click="canEdit = true">Editar</el-dropdown-item>
              <el-dropdown-item v-if="toBool(authUserPermissions[3])" @click="showConfirmModal = true">Eliminar</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
        <CancelButton v-if="canEdit" @click="canEdit = false">
          Cancelar edición
        </CancelButton>
        <PrimaryButton v-if="canEdit" type="button" @click="update()">Guardar cambios</PrimaryButton>
      </div>
    </form>
  </Modal>

  <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
    <template #title> Eliminar actividad </template>
    <template #content> ¿Continuar con la eliminación? </template>
    <template #footer>
      <div>
        <PrimaryButton @click="deleteOportunityTask">Eliminar</PrimaryButton>
        <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
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
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import axios from "axios";

export default {
  data() {
    const form = useForm({
      asigned_id: this.oportunityTask.asigned.id,
      limit_date: this.oportunityTask.limit_date_raw,
      time: this.oportunityTask.time_raw,
      priority: this.oportunityTask.priority,
      reminder: this.oportunityTask.reminder,
      description: this.oportunityTask.description,
      comment: null,
    });

    return {
      form,
      taskComponentLocal: null,
      oportunity: this.oportunityTask.oportunity.name,
      creator: this.oportunityTask.user?.name,
      taskInformationModal: false,
      showConfirmModal: false,
      sendingComments: false,
      canEdit: false,
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
    }
  },
  components: {
    Modal,
    PrimaryButton,
    CancelButton,
    InputError,
    ConfirmationModal,
    Link,
    RichText,
  },
  props: {
    oportunityTask: Object,
    users: Array,
  },
  methods: {
    updateComment(content) {
      this.form.comment = content;
    },
    async storeComment() {
      const editor = this.$refs.commentEditor;
      if (this.form.comment) {
        this.sendingComments = true;
        try {
          const response = await axios.post(route("oportunity-tasks.comment", this.taskComponentLocal.id), {
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
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    async update() {
      try {
        const response = await axios.put(route("oportunity-tasks.update", this.oportunityTask), {
          asigned_id: this.form.asigned_id,
          limit_date: this.form.limit_date,
          time: this.form.time,
          priority: this.form.priority,
          reminder: this.form.reminder,
          description: this.form.description,
        });
        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Se ha actualizado la actividad",
            type: "success",
          });
          this.taskComponentLocal = response.data.item;
          this.taskInformationModal = false;
          this.$emit('updated-oportunityTask', this.taskComponentLocal);
        }
      } catch (error) {
        console.log(error);
      }

    },
    getPriorityStyles() {
      if (this.oportunityTask?.priority === 'Baja') {
        return 'text-[#87CEEB]';
      } else if (this.oportunityTask?.priority === 'Media') {
        return 'text-[#D97705]';
      } else if (this.oportunityTask?.priority === 'Alta') {
        return 'text-[#D90537]';
      }
    },
    disabledDate(time) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return time.getTime() < today.getTime();
    },
    updateDescription(content) {
      this.form.description = content;
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
    async comment(oportunityTask) {
      try {
        const response = await axios.post(route("oportunity-tasks.comment", oportunityTask.id), {
          comment: this.form.comment,
        });
        if (response.status === 200) {
          this.oportunityTask?.comments.push(response.data.item);
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.form.comment = null;
      }
    },
    handleClick() {
      this.$emit('task-done', this.oportunityTask.id);
      this.taskInformationModal = false;
    },
    deleteOportunityTask() {
      this.$emit('delete-task', this.oportunityTask.id);
      this.taskInformationModal = false;
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
  },
  computed: {
    authUser() {
      console.log(this.users)
      return this.users.find(item => item.id == this.$page.props.auth.user.id);
    },
    authUserIsParticipant() {
      return this.taskComponentLocal.asigned?.id === this.authUser.id;
    },
    authUserPermissions() {
      const permissions = this.authUser.pivot.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    }
  },
  mounted() {
    this.taskComponentLocal = this.oportunityTask;
  },

}
</script>


