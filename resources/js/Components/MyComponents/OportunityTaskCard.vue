<template>
  <div @click="taskInformationModal = true" class="rounded-md shadow-md shadow-gray-400/100 group relative h-[196px] cursor-pointer">
  <el-tooltip :content="'Prioridad: ' + oportunityTask?.priority " placement="top">
        <i :class="getPriorityStyles()" class="fa-solid fa-circle text-[9px] absolute top-3 right-2 p-1"></i>
    </el-tooltip>
  <div class="py-3 px-4">
          <p :class="oportunityTask?.finished_at ? 'line-through' : ''">{{ oportunityTask?.name }}</p>
          <p class="text-gray-400 mt-3 mb-2">Asignado a</p>
          <figure>
              <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full items-center">
                <img class="h-11 w-11 rounded-full object-cover" :src="oportunityTask?.asigned?.profile_photo_url" :alt="oportunityTask?.asigned?.name" />
                <p class="ml-3">{{ oportunityTask?.asigned?.name }}</p>
              </div>
          </figure>
          <div class="flex items-center justify-between mt-3">
              <p :class="oportunityTask?.deadline_status === 'Atrasadas' ? 'bg-[#FEB2C4]' : 'bg-[#F6F89E]'" class="rounded-full px-3 text-sm">{{ oportunityTask?.deadline_status !== 'Terminar hoy' ? oportunityTask?.limit_date + ', ' + oportunityTask?.time : oportunityTask?.time }}</p>
            <div class="flex items-center space-x-3">
              <i v-if="oportunityTask?.finished_at" class="fa-solid fa-check text-lg text-green-500"></i>
              <p class="text-xs text-gray-400">{{ oportunityTask?.comments?.length }}<i class="fa-regular fa-comments text-lg rounded-full ml-2"></i></p>
            </div>
          </div>
       </div>
          <button @click.stop="$emit('task-done', oportunityTask?.id)" v-if="!oportunityTask?.finished_at" class="text-sm text-white bg-red-600 w-full py-1 rounded-b-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
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
          <label class="lg:pr-20">Asignado a</label>
          <el-select class="w-2/3" v-model="form.asigned_id" clearable filterable placeholder="Seleccionar usuario"
              no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="user in users" :key="user" :label="user.name" :value="user.id" />
          </el-select>
          <InputError :message="form.errors.asigned_id" />
      </div>

      <div class="lg:flex items-center space-x-4">
          <div class="flex items-center space-x-2">
          <label class="lg:mr-16 text-sm">Fecha limite</label>
          <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha limite *"
              :disabled-date="disabledDate" format="YYYY-MM-DD" />
          <InputError :message="form.errors.limit_date" />
          </div>
          <div class="flex items-center space-x-2">
              <label class="text-sm">Hora</label>
              <input v-model="form.time" class="input" type="time">
              <InputError :message="form.errors.time" />
          </div>
      </div>

      <div class="flex justify-between items-center space-x-3 text-sm relative">
          <label class="lg:mr-20">Prioridad</label>
          <el-select class="w-full mt-2" v-model="form.priority" clearable filterable placeholder="Seleccionar prioridad"
            no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
          <i :class="getColorPriority(form.priority)"
            class="fa-solid fa-circle text-xs top-3 left-16 absolute z-30"></i>
          <InputError :message="form.errors.priority" />
      </div>

      <div class="flex justify-between items-center space-x-2">
          <label class="text-sm">Recordatorio</label>
          <textarea v-model="form.reminder" disabled class="textarea w-3/4 cursor-not-allowed"> </textarea>
          <InputError :message="form.errors.reminder" />
        </div>

        <div class="flex justify-between items-center space-x-2">
          <label class="text-sm">Descripción</label>
          <textarea v-model="form.description" class="textarea w-3/4"> </textarea>
          <InputError :message="form.errors.description" />
        </div>

        <h2 class="font-bold py-5">Comentarios</h2>

        <!-- ------- Comments ------- -->
        <div class="mt-7">
            <div>
              <figure class="flex space-x-2 mt-4" v-for="comment in oportunityTask?.comments" :key="comment">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-12">
                  <img class="h-10 w-10 rounded-full object-cover" :src="comment.user?.profile_photo_url"
                    :alt="comment.user?.name" />
                </div>
                <div class="text-sm w-full">
                  <p class="font-bold">{{ comment.user?.name }}</p>
                  <p>{{ comment.body }}</p>
                </div>
              </figure>
              <div class="flex space-x-2 mt-5 items-center">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-12">
                  <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name" />
                </div>
                <textarea v-model="form.comment" class="textarea w-full"> </textarea>
                <PrimaryButton type="button" @click.stop="comment(oportunityTask)" class="h-9"><i
                    class="fa-regular fa-paper-plane"></i></PrimaryButton>
              </div>
            </div>
          </div>
      
      <div class="flex justify-start space-x-3 pt-5 pb-1">
        <el-dropdown split-button type="primary" @click="handleClick" class="custom-dropdown">
      Marcar como hecho
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item @click="update">Editar</el-dropdown-item>
          <el-dropdown-item @click="showConfirmModal= true">Eliminar</el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
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
import axios from "axios";

export default {
data(){
  const form = useForm({
      asigned_id: this.oportunityTask.asigned.id,
      limit_date: this.oportunityTask.limit_date_raw,
      time: this.oportunityTask.time_raw,
      priority: this.oportunityTask.priority,
      reminder: this.oportunityTask.reminder,
      description: this.oportunityTask.description,
      comment: null,
    });

    return{
      form,
      oportunity: this.oportunityTask.oportunity.name,
      creator: this.oportunityTask.user?.name,
      taskInformationModal: false,
      showConfirmModal: false,
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
components:{
  Modal,
  PrimaryButton,
  CancelButton,
  InputError,
  ConfirmationModal,
  Link,
},
props:{
    oportunityTask: Object,
    users: Array,
},
methods:{
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
          this.oportunityTask = response.data.item

        }
      } catch (error) {
        console.log(error);
      }
      this.$emit('updated-oportunityTask', this.oportunityTask.id);
        this.taskInformationModal = false;
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
        const today = new Date(); // Obtener la fecha de hoy
        return time.getTime() < today.getTime();
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
},

}
</script>

<style scoped>
.custom-dropdown {
  background-color: red; /* Fondo rojo */
  border-radius: 9999px; /* Redondeo completo */
  padding: 5px 20px; /* 5px de relleno vertical, 20px de relleno horizontal (ajusta según tus necesidades) */
}

.custom-dropdown-menu {
}

.custom-dropdown-item {
}
</style>