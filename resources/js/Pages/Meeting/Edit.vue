<template>
  <div>
    <AppLayout title="Reuniones - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar reunion "{{ meeting.subject }}"</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div>
            <IconInput
              v-model="form.subject"
              inputPlaceholder="Título de reunion *"
              inputType="text"
            >
              <el-tooltip content="Título de reunión *" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.subject" />
          </div>

          <div class="flex items-center">
            <el-tooltip content="Tipo de reunión" placement="top">
              <i class="fa-solid fa-people-arrows text-gray-700 mr-1"></i>
            </el-tooltip>
            <el-select v-model="form.type" placeholder="Tipo de reunión" style="width: 240px">
              <el-option v-for="item in types" :key="item" :label="item" :value="item" />
            </el-select>
            <InputError :message="form.errors.type" />
          </div>

          <div class="mb-3 flex items-center">
          <el-tooltip content="Fecha de reunión *" placement="top">
          <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
        </el-tooltip>
            <el-date-picker
              v-model="form.date"
              type="date"
              placeholder="Selecciona una fecha"
              :disabled-date="disabledDate"
            />
            <InputError :message="form.errors.date" />
          </div>
          <div class="flex items-center">
            <el-tooltip content="Participantes de la reunion" placement="top">
              <i class="fa-solid fa-users text-gray-700 mr-3"></i>
            </el-tooltip>
            <el-select
              v-model="form.participants"
              multiple
              placeholder="Participantes"
              style="width: 240px"
            >
              <el-option
                v-for="item in users"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
            <InputError :message="form.errors.participants" />
            <p @click="availableModal = true" class="text-primary ml-4 text-sm cursor-pointer">
              Ver disponibilidad de sherman
            </p>
          </div>
          <div class="md:flex">
            <div class="mr-3">
              <IconInput
              v-model="form.start"
              inputPlaceholder="Hora de comienzo *"
              inputType="time"
            >
              <el-tooltip content="Hora de comienzo *" placement="top">
                <i class="fa-solid fa-clock"></i>
              </el-tooltip>
            </IconInput>
              <InputError :message="form.errors.start" />
            </div>
            a
            <div class="ml-3">
              <IconInput
              v-model="form.end"
              inputPlaceholder="Hora de terminación *"
              inputType="time"
            >
            </IconInput>
              <InputError :message="form.errors.end" />
            </div>

            <!-- <label class="flex items-center">
              <Checkbox
                v-model:checked="form.repit"
                name="repit"
                class="bg-transparent"
              />
              <span class="ml-2 text-sm text-[#9A9A9A]">Repetir</span>
            </label> -->
          </div>
          <div v-if="form.type == 'Presencial'" class="w-3/5">
            <IconInput
              v-model="form.location"
              inputPlaceholder="Ubicación *"
              inputType="text"
            >
              <el-tooltip content="Lugar de la reunión *" placement="top">
                <i class="fa-solid fa-location-dot text-gray-700"></i>
              </el-tooltip>
            </IconInput>

            <InputError :message="form.errors.location" />
          </div>

          <div v-if="form.type == 'Videoconferencia'" class="w-full">
            <IconInput
              v-model="form.url"
              inputPlaceholder="URL"
              inputType="text"
            >
              <el-tooltip content="URL de meeting en caso de ser en linea" placement="top">
                <i class="fa-solid fa-chain text-gray-700"></i>
              </el-tooltip>
            </IconInput>

            <InputError :message="form.errors.url" />
          </div>

          <!-- <div class="flex items-center">
            <IconInput
              v-model="form.subject"
              inputPlaceholder="Recordario"
              inputType="text"
              class="w-1/5"
            >
              <el-tooltip content="Recordatorios" placement="top">
                <i class="fa-solid fa-stopwatch text-gray-700"></i>
              </el-tooltip>
            </IconInput>

            <InputError :message="form.errors.location" />

            <p class="text-primary ml-4 text-sm cursor-pointer">
              Agregar recordatorio
            </p>
          </div> -->

          <div class="flex col-span-full">
            <el-tooltip content="Descripción" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
              >
                ...
              </span>
              
            </el-tooltip>
            <textarea
              v-model="form.description"
              class="textarea"
              autocomplete="off"
              placeholder="Descripción"
            ></textarea>
            <InputError :message="form.errors.description" />
          </div>

          <div class="pt-5 mx-3 md:text-left">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Guardar cambios
            </PrimaryButton>
          </div>
        </div>
      </form>

      <!-- -------------- Modal starts----------------------- -->
      <Modal :show="availableModal" @close="availableModal = false">
        <div class="p-3 relative">
          <i
            @click="availableModal = false"
            class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"
          ></i>
          <i
            v-if="!helpDialog"
            @click="helpDialog = true"
            class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-3 top-3 cursor-pointer"
          ></i>

          <p class="font-bold text-center">Disponibilidad de Sherman</p>

          <p class="ml-7 my-6 text-secondary">Disponibilidad del día de hoy</p>

          <div class="text-center rounded-lg border border-[#9a9a9a] w-1/3 mx-auto relative">
            <div @click="selectTime(hour)" v-for="(hour, index) in schedule" :key="hour" class="cursor-pointer border-b border-[#9a9a9a] h-[25px] hover:bg-secondary hover:text-white">{{ index % 2 ? '' : hour }}</div>
          </div> 

          <div
            v-if="helpDialog"
            class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mx-7 relative"
          >
            <i
              class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"
            ></i>
            <i
              @click="helpDialog = false"
              class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"
            ></i>
            <p class="text-secondary text-sm">
              Es necesario describir las actividades que justifiquen el tiempo
              adicional que estas solicitando. Sólo se podrá realizar una
              solicitud por semana, por lo que debes de ingresar las horas
              semanales adicionales a tu jornada normal.
            </p>
          </div>

          <div class="flex justify-start space-x-3 pt-5 pb-1">
            <PrimaryButton @click="availableModal = false">Listo</PrimaryButton>
          </div>
        </div>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      subject: this.meeting.subject,
      type: this.meeting.type,
      location: this.meeting.location,
      url: this.meeting.url,
      description: this.meeting.description,
      date: this.meeting.date,
      start: this.meeting.start.raw,
      end: this.meeting.end.raw,
      repit: this.meeting.repit,
      participants: this.meeting.participants,
    });

    return {
      form,
      availableModal: false,
      helpDialog: false,

      types:[
        'Videoconferencia',
        'Presencial',
        'Vía telefónica',
      ],

      schedule:[
          '9:00',
          '9:30',
          '10:00',
          '10:30',
          '11:00',
          '11:30',
          '12:00',
          '12:30',
          '12:00',
          '12:30',
          '13:00',
          '13:30',
          '14:00',
          '14:30',
          '15:00',
          '15:30',
          '16:00',
          '16:30',
          '17:00',
          '17:30',
          '18:00',
          '18:30',
      ]
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Checkbox,
    Modal,
    Back,
    Link,
  },
  props: {
    users: Array,
    meeting: Object,
  },
  methods: {
    update() {
      this.form.put(route("meetings.update", this.meeting), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Reunion editada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    selectTime(hour){
      console.log(hour);
      // this.form.start = hour;
    },
  },
};
</script>
