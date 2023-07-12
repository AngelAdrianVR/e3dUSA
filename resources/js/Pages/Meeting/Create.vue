<template>
  <div>
    <AppLayout title="Reuniones - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('meetings.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Agendar reunion</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div>
            <IconInput
              v-model="form.subject"
              inputPlaceholder="Título de reunion *"
              inputType="text"
            >
              <el-tooltip content="Título de reunión" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.subject" />
          </div>

          <div class="mb-3">
            <el-date-picker
              v-model="form.date"
              type="date"
              placeholder="Selecciona una fecha"
            />
            <InputError :message="form.errors.date" />
          </div>
          <div class="md:flex">
            <div>
              <el-time-picker
                v-model="form.start"
                placeholder="Hora de comienzo"
                format="HH:mm"
              />
              <InputError :message="form.errors.start" />
            </div>
            a
            <div class="ml-7">
              <el-time-picker
                v-model="form.end"
                placeholder="Hora de finalización"
                format="HH:mm"
              />
              <InputError :message="form.errors.end" />
            </div>

            <label class="flex items-center">
              <Checkbox
                v-model:checked="form.repit"
                name="repit"
                class="bg-transparent"
              />
              <span class="ml-2 text-sm text-[#9A9A9A]">Repetir</span>
            </label>
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
              Ver disponibilidad
            </p>
          </div>

          <div class="w-3/5">
            <IconInput
              v-model="form.subject"
              inputPlaceholder="Ubicación"
              inputType="text"
            >
              <el-tooltip content="Lugar de la reunion" placement="top">
                <i class="fa-solid fa-location-dot text-gray-700"></i>
              </el-tooltip>
            </IconInput>

            <InputError :message="form.errors.location" />
          </div>

          <div class="flex items-center">
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
          </div>

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
              Agendar reunion
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
          <!-- ---------------- 9am ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-1">9:00 Am</span>
            <div class="border-b border-[#9a9a9a] h-[30px]"></div>

          <!-- ---------------- 10am ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-8">10:00 Am</span>
            <div class="border-b border-[#9a9a9a] h-[30px]"></div>

          <!-- ---------------- 11am ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-16">11:00 Am</span>
            <div class="border-b border-[#9a9a9a] h-[30px] bg-primary"></div>

          <!-- ---------------- 12pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-24">12:00 Pm</span>
            <div class="border-b border-[#9a9a9a] h-[30px] bg-primary"></div>

          <!-- ---------------- 1pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-32">1:00 Pm</span>
            <div class="border-b border-[#9a9a9a] h-[30px]"></div>

          <!-- ---------------- 2pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-40">2:00 Pm</span>
            <div class="border-b border-[#9a9a9a] h-[30px] bg-primary"></div>

          <!-- ---------------- 3pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-48">3:00 Pm</span>
            <div class="border-b border-[#9a9a9a] h-[30px]"></div>

          <!-- ---------------- 4pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-56">4:00 Pm</span>
            <div class="border-b border-[#9a9a9a] h-[30px]"></div>

          <!-- ---------------- 5pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-64">5:00 Pm</span>
            <div class="border-b border-[#9a9a9a] h-[30px] bg-primary"></div>

          <!-- ---------------- 6pm ---------------- -->
            <span class="absolute text-sm text-[#9a9a9a] -left-20 top-72">5:00 Pm</span>
            <div class="h-[30px]"></div>

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
  },
};
</script>
