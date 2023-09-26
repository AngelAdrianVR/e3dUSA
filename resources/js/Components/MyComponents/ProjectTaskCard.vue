<template>
  <div @click="taskInformationModal = true; itemToShow = taskComponent" :class="taskComponent.priority.color_border" class="shadow-md shadow-gray-400/100 h-36 rounded-r-md border-l-4 py-2 px-3 cursor-pointer my-3">
            <!-- ------------ top ------------------ -->
<el-tooltip :content="'Prioridad: ' + taskComponent.priority.label" placement="top">
                <div class="flex justify-between items-center">
                    <div @click.stop="" class="rounded-full px-2 cursor-grab active:cursor-grabbing">
                        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                    </div>
                    <div @click.stop="" class="flex cursor-default">
                      <p v-if="taskComponent.is_paused" class="mr-4 rounded-full text-orange-500 bg-orange-200 px-2">{{ 'Pausado' }}</p>
                      <p class="mr-5">{{ taskComponent.created_at }}</p>
                    </div>
                </div>
</el-tooltip>
            <!-- ------------ body -------------------------- -->
                <div class="flex items-center justify-between p-3">
                    <p class="text-lg">{{ taskComponent.title }}</p>
                    <div>
                        <!-- <el-tooltip content="Tienes una tarea por cumplir antes de poder comenzar" placement="top">
                            <i @click.stop="" class="fa-solid fa-hourglass cursor-default mr-3"></i>
                        </el-tooltip> -->
                        <el-tooltip v-if="taskComponent.media.length" content="Archivo adjunto" placement="top">
                            <i @click.stop="" class="fa-solid fa-paperclip rounded-full p-2"></i>
                        </el-tooltip>
                    </div>
                </div>

            <!-- ----------------- footer --------------- -->
                <footer class="p-3 border-t border-[#9A9A9A] relative">
                    <div class="flex justify-between items-center px-3">
                      <div @click.stop="" class="flex items-end text-[#9A9A9A]">
                        <i class="fa-regular fa-comments text-lg  rounded-full py-1 px-2"></i>
                       <p class="text-xs ml-1">{{ taskComponent.comments?.length }} </p>
                       <p class="text-sm ml-3">| {{ 'Dpto. ' + taskComponent.department }} </p>
                      </div>
                      <div class="flex items-center absolute bottom-3 right-0 cursor-default">
                      <el-tooltip v-if="taskComponent.status == 'Terminada'" content="Tarea terminada" placement="bottom">
                        <i class="fa-solid fa-check text-green-500 text-xl cursor-default mr-2"></i>
                    </el-tooltip>
                        <!-- <p class="text-primary mr-1">+2</p> -->
                        <el-tooltip v-for="user in taskComponent.participants" :key="user" :content="user.name" placement="bottom">
                            <figure @click.stop="">
                                <div v-if="$page.props.jetstream.managesProfilePhotos"
                                    class="flex text-sm rounded-full">
                                    <img class="h-9 w-9 rounded-full object-cover" :src="user.profile_photo_url"
                                    :alt="user.name" />
                                </div>
                            </figure>
                        </el-tooltip>
                      </div>
                    </div>
                </footer>
            </div>

            <!-- -------------- task information Modal -------------- -->
      <Modal :show="taskInformationModal" @close="taskInformationModal = false">
        <form @submit.prevent="update" class="mx-7 my-4 space-y-4 relative">
              <div @click="taskInformationModal = false"
                class="cursor-pointer w-5 h-5 rounded-full flex items-center justify-center absolute top-0 right-0">
                <i class="fa-solid fa-xmark"></i>
              </div>
                <h1 class="font-bold">{{ taskComponent.title }}</h1>
                
                <div>
                    <label>Estado actual</label> <br>
                    <div class="flex items-center space-x-4">
                        <el-select :disabled="taskComponent.is_paused" class="lg:w-1/2 mt-2" v-model="form.status" clearable filterable placeholder="Seleccionar estatus"
                            no-data-text="No hay estatus registrados" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
                                <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                                <span style="float: center; margin-left: 5px; font-size: 13px;">{{ item.label}}</span>
                            </el-option>
                        </el-select>
                        <el-tooltip v-if="taskComponent.status == 'En curso'" :content="taskComponent.is_paused ? 'Reanudar tarea' : 'Pausar tarea'" placement="top">
                            <button type="button"><i @click.stop="$inertia.put(route('tasks.pause-play', taskComponent))" :class="taskComponent.is_paused ? 'fa-circle-play' : 'fa-circle-pause'" class="fa-regular text-secondary text-xl cursor-pointer"></i></button>
                        </el-tooltip>
                    </div>
                    <InputError :message="form.errors.status" />
                </div>
                <h2 class="font-bold">Información de la tarea</h2>
                <div>
                    <div class="flex space-x-2 justify-end items-center">
                        <label>Proyecto</label>
                        <input v-model="form.project_name" disabled class="input w-[80%]" type="text">
                        <InputError :message="form.errors.project_name" />
                    </div>
                    <div class="flex space-x-2 justify-end items-center mt-3">
                        <label>Creado por</label>
                        <input v-model="form.user" disabled class="input w-[80%]" type="text">
                        <InputError :message="form.errors.user" />
                    </div>  
                    <div class="flex space-x-2 justify-end items-center mt-3">
                        <label>Departamento</label>
                        <el-select class="w-full mt-2" v-model="form.department" clearable filterable placeholder="Seleccionar departamento"
                            no-data-text="No hay departamentos registrados" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in departments" :key="item" :label="item" :value="item" />
                        </el-select>
                <InputError :message="form.errors.department" />
                    </div>  
                    <div class="flex space-x-2 justify-end items-center mt-3">
                        <label>Participantes</label>
                        <el-select class="w-full mt-2" v-model="form.participants" clearable filterable multiple placeholder="Seleccionar participantes"
                    no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id" />
                </el-select>
                <InputError :message="form.errors.participants" />
                    </div>
                    <div class="mt-3">
                        <label>Descripción</label>
                        <textarea v-model="form.description" class="textarea w-full">
                        </textarea>
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="mt-3">
                    <label>Prioridad</label>
                    <el-select class="w-full mt-2" v-model="form.priority" clearable filterable placeholder="Seleccionar prioridad"
                        no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
                            <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                            <span style="float: center; margin-left: 5px; font-size: 13px;">{{ item.label}}</span>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.priority" />
                </div>
                <div class="flex space-x-5 justify-between mt-3">
                    <div class="w-1/2">
                        <label>fecha de inicio</label>
                        <input v-model="form.start_date" disabled class="input" type="text">
                        <InputError :message="form.errors.start_date" />
                    </div>   
                    <div class="w-1/2">
                        <label>fecha final</label>
                        <input v-model="form.limit_date" disabled class="input" type="text">
                        <InputError :message="form.errors.limit_date" />
                    </div>   
                </div>  

                <div class="w-1/2 mt-3">
                        <label>Recordatorio</label>
                        <textarea v-model="form.reminder" disabled class="textarea w-full">
                        </textarea>
                        <InputError :message="form.errors.reminder" />
                    </div>  
    <!-- --------------------- TABS -------------------- -->
                    <section class="mt-9">
                        <div class="flex items-center">
                            <p @click="tabs = 1" :class="tabs == 1 ? 'border-b-2 border-[#D90537] text-primary' : ''
                                " class="h-8 p-1 cursor-pointer ml-5 transition duration-300 ease-in-out text-xs md:text-base">
                                 Comentarios ({{ taskComponent.comments?.length }})
                            </p>
                            <div class="border-r-2 border-[#cccccc] h-7 ml-3"></div>
                            <p @click="tabs = 2" :class="tabs == 2 ? 'border-b-2 border-[#D90537] text-primary' : ''
                                " class="ml-3 h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
                                Documentos
                            </p>
                            <div class="border-r-2 border-[#cccccc] h-7 ml-3"></div>
                            <p @click="tabs = 3" :class="tabs == 3 ? 'border-b-2 border-[#D90537] text-primary' : ''
                                " class="ml-3 h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
                                Historial
                            </p>
                            <div class="border-r-2 border-[#cccccc] h-7 ml-3"></div>
                            <p @click="tabs = 4" :class="tabs == 4 ? 'border-b-2 border-[#D90537] text-primary' : ''
                                " class="ml-3 h-8 p-1 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
                                Dependencia/Consecutiva
                            </p>
                        </div>
            <!-- -------------- Tab 1 comentarios starts ----------------->
                        <div v-if="tabs == 1" class="mt-7">
                            <div>
                                <figure class="flex space-x-2 mt-4"  v-for="comment in taskComponent.comments" :key="comment">
                                <div v-if="$page.props.jetstream.managesProfilePhotos"
                                    class="flex text-sm rounded-full w-12">
                                    <img class="h-10 w-10 rounded-full object-cover" :src="comment.user.profile_photo_url"
                                    :alt="comment.user.name" />
                                </div>
                                <div class="text-sm w-full">
                                    <p class="font-bold">{{ comment.user.name }}</p>
                                    <p>{{ comment.body }}</p>
                                </div>
                            </figure>
                            <div class="flex space-x-2 mt-5 items-center">
                                <div v-if="$page.props.jetstream.managesProfilePhotos"
                                    class="flex text-sm rounded-full w-12">
                                    <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                                    :alt="$page.props.auth.user.name" />
                                </div>
                                <textarea v-model="form.comment" class="textarea w-full">
                                </textarea>
                                <PrimaryButton type="button" @click.stop="comment(taskComponent)" class="h-9"><i class="fa-regular fa-paper-plane"></i></PrimaryButton>
                            </div>
                            </div>
                        </div>
            <!-- ---------------- tab 1 comentarios ends  -------------->


            <!-- -------------- Tab 2 documentos starts ----------------->
                        <div v-if="tabs == 2" class="mt-7">
                            <a :href="file?.original_url" target="_blank" v-for="file in taskComponent.media" :key="file" class="flex justify-between items-center cursor-pointer">
                               <div class="flex space-x-7 items-center">
                                    <img src="@/../../public/images/adobepdf.png" :alt="file?.file_name">
                                    <p>{{ file?.file_name }}sss</p>
                               </div>
                                <i class="fa-solid fa-download text-right text-sm text-[#9a9a9a]"></i>
                            </a>
                        </div>
            <!-- ---------------- tab 2 documentos ends  -------------->


            <!-- -------------- Tab 3 historial starts ----------------->
                        <div v-if="tabs == 3" class="mt-7">

                        </div>
            <!-- ---------------- tab 3 historial ends  -------------->


            <!-- -------------- Tab 4 dependencia/consecutiva starts ----------------->
                        <div v-if="tabs == 4" class="mt-7">

                        </div>
            <!-- ---------------- tab 4 dependencia/consecutiva ends  -------------->
                    </section>
                </div>
{{ taskComponent }}
          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <PrimaryButton>Guardar</PrimaryButton>
            <CancelButton @click="taskInformationModal = false">Cancelar</CancelButton>
          </div>
        </form>
      </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';
export default {
data(){
    const form = useForm({
      status: this.taskComponent.status,
      title: null,
      project_name: this.taskComponent.project.project_name,
      user: this.taskComponent.user.name,
      department: this.taskComponent.department,
      participants: this.taskComponent.participants,
      description: this.taskComponent.description,
      priority: this.taskComponent.priority.label,
      start_date: this.taskComponent.start_date,
      limit_date: this.taskComponent.end_date,
      reminder: this.taskComponent.reminder,
      comment: null,
          });

    return {
        form,
        tabs: 1,
        taskInformationModal: false,
        itemToShow: null,
        statuses: [
            {
              label: 'Por hacer',
              color: 'text-[#9A9A9A]',
            },
            {
              label: 'En curso',
              color: 'text-secondary',
            },
            {
              label: 'Terminada',
              color: 'text-green-500',
            },
        ],
        priorities: [
            {
              label: 'Baja',
              color: 'text-[#87CEEB]',
            },
            {
              label: 'Media',
              color: 'text-orange-500',
            },
            {
              label: 'Alta',
              color: 'text-red-600',
            },
        ],
        departments:[
        'Compras',
        'Ventas',
        'Producción',
        'Diseño',
      ],
    }
},
components:{
    Modal,
    PrimaryButton,
    CancelButton,
    Link,
},
props:{
    taskComponent: Object,
    users: Array
},
methods:{
    update() {
      this.form.put(route("tasks.update", this.taskComponent), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha actualizado la tarea tarea",
            type: "success",
          });
            this.taskInformationModal = false;
        },
      });
    },
    async comment(task){
        try {
            const response = await axios.post(route('tasks.comment', task.id), {
                comment: this.form.comment
            })
            if(response.status === 200){
               this.taskComponent.comments.push(response.data.item);
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.form.comment = null;
        }
    },
},

}
</script>
