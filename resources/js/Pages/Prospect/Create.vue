<template>
  <div>
    <AppLayout title="Crear prospecto">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear prospecto</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store" class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md">
        <h1 class="my-4 font-bold">Nuevo prospecto</h1>
        <div class="grid grid-cols-2 gap-x-3">
          <div class="self-end">
            <label class="flex items-center text-sm ml-2 space-x-2">
              <span>Nombre de la empresa *</span>
              <el-tooltip content="Agrega un nombre para identificar el prospecto" placement="top">
                <div class="rounded-full border border-primary size-3 flex items-center justify-center">
                  <i class="fa-solid fa-info text-primary text-[7px]"></i>
                </div>
              </el-tooltip>
            </label>
            <input v-model="form.name" class="input" type="text" placeholder="Escribe el nombre de la empresa">
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <label class="text-sm ml-2">Domicilio</label>
            <input v-model="form.address" class="input" type="text" placeholder="Escribe el domicilio">
            <InputError :message="form.errors.address" />
          </div>
          <div>
            <label class="text-sm ml-2">Estado</label>
            <el-select v-model="form.state" filterable placeholder="Selecciona el estado de la república" class="w-full"
              no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in states" :key="index" :label="item" :value="item" />
            </el-select>
            <InputError :message="form.errors.state" />
          </div>
          <div>
            <label class="text-sm ml-2">Nombre del contacto *</label>
            <input v-model="form.contact_name" class="input" type="text" placeholder="Escribe el nombre del contacto">
            <InputError :message="form.errors.contact_name" />
          </div>
          <div>
            <label class="text-sm ml-2">Puesto *</label>
            <input v-model="form.contact_charge" class="input" type="text" placeholder="Escribe el puesto">
            <InputError :message="form.errors.contact_charge" />
          </div>
          <div>
            <label class="text-sm ml-2">Correo electrónico *</label>
            <input v-model="form.contact_email" class="input" type="text" placeholder="Agrega el Correo electrónico">
            <InputError :message="form.errors.contact_email" />
          </div>
          <div>
            <label class="text-sm ml-2">Teléfono *</label>
            <div class="flex items-center space-x-2">
              <el-input v-model="form.contact_phone" placeholder="Escribe el teléfono"
                :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable />
              <label class="text-sm">Ext.</label>
              <el-input v-model="form.contact_phone_extension" placeholder="" class="!w-[45%]"
                :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                :parser="(value) => value.replace(/\D/g, '')" maxlength="5" clearable />
            </div>
            <InputError :message="form.errors.contact_phone" />
          </div>
          <div class="self-end">
            <label class="flex items-center text-sm ml-2 space-x-2">
              <status>Estatus *</status>
              <el-tooltip :content="statuses.find(item => item.label == form.status).tooltip" placement="top">
                <i class="fa-solid fa-circle text-[10px] mt-1"
                  :style="{ color: statuses.find(item => item.label == form.status).color }"></i>
              </el-tooltip>
            </label>
            <el-select v-model="form.status" placeholder="Selecciona el estatus" class="w-full"
              no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in statuses" :key="index" :label="item.label" :value="item.label">
                <p class="flex items-center space-x-2">
                  <i class="fa-solid fa-circle text-[10px] mt-1" :style="{ color: item.color }"></i>
                  <span class="text-sm">{{ item.label }}</span>
                </p>
              </el-option>
            </el-select>
            <InputError :message="form.errors.status" />
          </div>
          <div>
            <label class="text-sm ml-2">Número de sucursales</label>
            <el-input v-model="form.branches_number" placeholder="Escribe el numero de sucursales "
              :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
              :parser="(value) => value.replace(/\D/g, '')" maxlength="3" />
            <InputError :message="form.errors.branches_number" />
          </div>
          <div v-if="form.status != 'Contacto inicial'">
            <label class="text-sm ml-2">Responsable</label>
            <el-select v-model="form.seller_id" placeholder="Seleccione" class="w-full"
              no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in sellers" :key="item.id" :label="item.name" :value="item.id">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                  class="flex text-sm rounded-full items-center mt-[3px]">
                  <img class="size-7 rounded-full object-cover mr-4" :src="item.profile_photo_url" :alt="item.name" />
                  <p>{{ item.name }}</p>
                </div>
              </el-option>
            </el-select>
            <InputError :message="form.errors.seller_id" />
          </div>
        </div>
        <div>
          <label class="text-sm ml-2">Resumen</label>
          <textarea v-model="form.abstract" rows="3" class="textarea"
            placeholder="Escribe un resumen de los productos que le interesen al cliente, de lo más relevate en la charla o en caso de ser prospecto perdido, colocar el motivo"></textarea>
          <InputError :message="form.errors.abstract" />
        </div>
        <div class="mt-5 md:text-right">
          <PrimaryButton :disabled="form.processing">
            Crear prospecto
          </PrimaryButton>
        </div>
      </form>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: null,
      address: null,
      state: null,
      contact_name: null,
      contact_charge: null,
      contact_email: null,
      contact_phone: null,
      contact_phone_extension: null,
      status: 'Contacto inicial',
      branches_number: null,
      abstract: null,
      seller_id: null,
    });

    return {
      form,
      statuses: [
        {
          label: "Contacto inicial",
          bg: "#F1F996",
          color: "#B1B402",
          tooltip: "El prospecto entra en contacto con la empresa por primera vez",
        },
        {
          label: "Asignado",
          bg: "#F9BA96",
          color: "#F07209",
          tooltip: "Se asignó a un responsable para gestionar el seguimiento con el prospecto",
        },
        {
          label: "En proceso de conversión",
          bg: "#BCF996",
          color: "#37A305",
          tooltip: "El responsable esta trabajando para convertir el prospecto en nuevo cliente",
        },
        {
          label: "Perdido",
          bg: "#F7B7FC",
          color: "#9E0FA9",
          tooltip: "El prospecto no se convierte en cliente ",
        },
      ],
      states: [
        'Aguascalientes',
        'Baja California',
        'Baja California Sur',
        'Campeche',
        'Chiapas',
        'Chihuahua',
        'Ciudad de México',
        'Coahuila',
        'Colima',
        'Durango',
        'Estado de México',
        'Guanajuato',
        'Guerrero',
        'Hidalgo',
        'Jalisco',
        'Michoacán',
        'Morelos',
        'Nayarit',
        'Nuevo León',
        'Oaxaca',
        'Puebla',
        'Querétaro',
        'Quintana Roo',
        'San Luis Potosí',
        'Sinaloa',
        'Sonora',
        'Tabasco',
        'Tamaulipas',
        'Tlaxcala',
        'Veracruz',
        'Yucatán',
        'Zacatecas',
      ],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    CancelButton,
    Link,
    Back,
  },
  props: {
    sellers: Array,
  },
  methods: {
    store() {
      this.form.post(route("prospects.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo prospecto",
            type: "success",
          });

        },
      });
    },
  },
  mounted() {

  }
};
</script>