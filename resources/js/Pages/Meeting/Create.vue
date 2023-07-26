<template>
  <div>
    <AppLayout title="Reuniones - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('meetings.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Agendar reunion</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.subject" inputPlaceholder="Título de reunion *" inputType="text">
              <el-tooltip content="Título de reunión *" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.subject" />
          </div>

          <div class="mb-3 flex items-center">
            <el-tooltip content="Fecha de reunión *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.date" type="date" placeholder="Selecciona una fecha" :disabled-date="disabledDate" />
            <InputError :message="form.errors.date" />
          </div>
          <div class="flex">
            <div class="mr-3">
              <IconInput v-model="form.start" inputPlaceholder="Hora de comienzo *" inputType="time">
                <el-tooltip content="Hora de comienzo *" placement="top">
                  <i class="fa-solid fa-clock"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.start" />
            </div>
            a
            <div class="ml-3">
              <IconInput v-model="form.end" inputPlaceholder="Hora de terminación *" inputType="time">
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

          <div class="flex items-center">
            <el-tooltip content="Participantes de la reunion" placement="top">
              <i class="fa-solid fa-users text-gray-700 mr-3"></i>
            </el-tooltip>
            <el-select v-model="form.participants" multiple placeholder="Participantes" style="width: 240px">
              <el-option v-for="item in users" :key="item.id" :label="item.name" :value="item.name" />
            </el-select>
            <InputError :message="form.errors.participants" />
            <p @click="availableModal = true" class="text-primary ml-4 text-sm cursor-pointer">
              Ver disponibilidad de sherman
            </p>
          </div>

          <div class="w-3/5">
            <IconInput v-model="form.location" inputPlaceholder="Ubicación *" inputType="text">
              <el-tooltip content="Lugar de la reunión. Si es en linea, indicarlo *" placement="top">
                <i class="fa-solid fa-location-dot text-gray-700"></i>
              </el-tooltip>
            </IconInput>

            <InputError :message="form.errors.location" />
          </div>

          <div class="w-full">
            <IconInput v-model="form.url" inputPlaceholder="URL" inputType="text">
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
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                ...
              </span>
            </el-tooltip>
            <textarea v-model="form.description" class="textarea" autocomplete="off" placeholder="Descripción"></textarea>
            <InputError :message="form.errors.description" />
          </div>

          <div class="pt-5 mx-3 md:text-left">
            <PrimaryButton :disabled="form.processing">
              Agendar reunión
            </PrimaryButton>
          </div>
        </div>
      </form>

      <!-- -------------- Modal starts----------------------- -->
      <Modal :show="availableModal" @close="availableModal = false">
        <div class="p-3 relative">
          <i @click="availableModal = false"
            class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
          <i v-if="!helpDialog" @click="helpDialog = true"
            class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-3 top-3 cursor-pointer"></i>

          <p class="font-bold text-center mb-5">Disponibilidad de Sherman</p>

          <div class="mb-3 flex items-center justify-start">
            <el-tooltip content="Fecha de reunión *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.date" type="date" placeholder="Selecciona una fecha" :disabled-date="disabledDate" />
            <p v-if="!form.date" class="text-xs">Selecciona una fecha</p>
          </div>

          <div v-if="helpDialog" class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mx-7 relative">
            <i
              class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
            <i @click="helpDialog = false"
              class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"></i>
            <p class="text-secondary text-sm">
              Las secciones en color rojos no están disponibles para agendar reunión.
              Las secciones azules son las horas que seleccionaste para tu reunión.
            </p>
          </div>

          <section v-if="form.date">
            <p v-if="!start_selected" class="ml-7 my-6 text-secondary">Selecciona la hora de inicio</p>
            <p v-else class="ml-7 my-6 text-secondary">Selecciona la hora de terminación</p>

            <p class="ml-7 my-6 text-primary text-center">De {{ form.start ?? '____' }} a {{ form.end ?? '____' }}</p>



            <div class="text-center rounded-lg border border-[#9a9a9a] lg:w-1/3 mx-auto relative">
              <div @click="selectTime(meeting, index)" v-for="(meeting, index) in schedule" :key="meeting"
                :class="meeting.bg + ' ' + meeting.text"
                class="cursor-pointer border-b border-[#9a9a9a] h-[25px] hover:bg-secondary hover:text-white">{{ index % 2
                  ? '' : meeting.label }}</div>
            </div>


          </section>

          <div class="flex justify-start space-x-3 pt-5 pb-1">
            <PrimaryButton @click="availableModal = false">Listo</PrimaryButton>
            <CancelButton v-if="form.start" @click="reset()">Borrar</CancelButton>
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
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";

export default {
  data() {
    const form = useForm({
      subject: null,
      location: null,
      url: null,
      description: null,
      date: null,
      start: null,
      end: null,
      repit: null,
      participants: null,
    });

    return {
      form,
      availableModal: false,
      helpDialog: false,
      start_selected: false,
      start_index: null,

      schedule: [
        {
          'label': '09:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '09:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '10:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '10:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '11:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '11:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '12:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '12:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '13:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '13:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '14:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '14:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '15:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '15:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '16:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '16:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '17:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '17:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '18:00',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
        {
          'label': '18:30',
          'bg': 'bg-transparent',
          'text': 'text-black'
        },
      ]
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
    Modal,
    CancelButton
  },
  props: {
    users: Array,
  },
  methods: {
    store() {
      this.form.post(route("meetings.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Reunion agendada",
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
    selectTime(meeting, index) {

      if (this.form.start && this.form.end) {
        return
      }

      if (this.start_selected == false) {

        this.start_index = index;
        this.form.start = meeting.label;
        this.schedule[index].bg = 'bg-secondary';
        this.schedule[index].text = 'text-white';
        this.start_selected = true;
      } else {

        this.form.end = meeting.label;
        for (let i = this.start_index; i <= index; i++) {
          this.schedule[i].bg = 'bg-secondary';
          this.schedule[i].text = 'text-white';
        }

        this.start_selected = false;
      }
    },
    reset() {
      for (let i = 0; i < this.schedule.length; i++) {
        this.schedule[i].bg = 'bg-transparent';
        this.schedule[i].text = 'text-black';
      }
      // this.form.date = null;
      this.form.start = null;
      this.form.end = null;
      this.start_selected = false;
    }
  },
};
</script>
