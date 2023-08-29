<template>
  <div class="rounded-xl bg-[#cccccc] px-7 py-3 relative text-xs shadow-lg">
    <!-- selection circle -->
    <div @click="handleSelection" v-if="!is_view_for_seller"
      class="w-5 h-5 border-2 rounded-full absolute top-3 left-3 cursor-pointer flex items-center justify-center"
      :class="selected ? 'border-[#d90537]' : 'border-black'">
      <div class="w-3 h-3 rounded-full" :class="selected ? 'bg-primary' : null"></div>
    </div>

    <!-- card -->
    <p class="text-center font-bold mt-4 text-sm">
      {{ catalog_product_company_sale.catalog_product_company?.catalog_product?.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{
      catalog_product_company_sale.catalog_product_company?.catalog_product?.part_number
    }}</span>
    <el-tooltip content="Número de parte" placement="top">
      <i
        class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>

    <div class="grid grid-cols-2 gap-x-4">
      <figure class="bg-[#D9D9D9] w-full h-28 my-3 rounded-[10px]">
        <el-image style="height: 100%; border-radius: 10px;"
          :src="catalog_product_company_sale.catalog_product_company?.catalog_product?.media[0]?.original_url"
          fit="contain">
          <template #error>
            <div class="flex justify-center items-center text-[#ababab]">
              <i class="fa-solid fa-image text-6xl"></i>
            </div>
          </template>
        </el-image>
      </figure>
      <div>
        <p class="text-primary text-left">Caracteristicas</p>
        <li v-for="(feature, index) in catalog_product_company_sale.catalog_product_company?.catalog_product?.features"
          :key="index" class="text-gray-800 list-disc">{{ feature }}</li>
      </div>
    </div>

    <div class="border-b-2 border-[#9a9a9a] pb-1">
      <p class="text-primary ">Unidades ordenadas: <span class="ml-4 text-black">{{
        catalog_product_company_sale.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      }}</span></p>
    </div>

    <div v-if="!is_view_for_seller" class="border-b-2 border-[#9a9a9a] pb-1 mt-2">
      <p class="text-primary ">Operadores asignados:</p>
      <p v-for="production in catalog_product_company_sale.productions" :key="production.id"
        class="mt-1 flex justify-between items-center">
        <span :class="$page.props.auth.user.id == production.operator.id ? 'text-green-600' : null">-{{
          production.operator.name }} {{ $page.props.auth.user.id == production.operator.id ? '(Tú)' : '' }}
        </span>
        <span v-if="production.is_paused"
          class="text-[10px] text-yellow-600 bg-yellow-200 rounded-full px-1">pausado</span>
        <el-tooltip placement="right">
          <template #content>
            <p> <strong class="text-yellow-500">Tareas: </strong>{{ production.tasks }}</p>
            <p> <strong class="text-yellow-500">Tiempo estimado: </strong>{{ production.estimated_time_hours }}h {{
              production.estimated_time_minutes }}m</p>
            <p> <strong class="text-yellow-500">Empezado el: </strong>{{ production.started_at ?
              getDateFormtted(production.started_at) : 'Sin iniciar' }}</p>
            <p> <strong class="text-yellow-500">Terminado el: </strong>{{ production.finished_at ?
              getDateFormtted(production.finished_at) : 'Sin terminar' }}</p>
          </template>
          <i class="fa-solid fa-list-check"></i>
        </el-tooltip>
      </p>
    </div>

    <div class="bg-[#d9d9d9] rounded-lg p-2 grid grid-cols-2 my-3">
      <span class="">Precio Anterior:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company?.old_price }}
        {{ catalog_product_company_sale.catalog_product_company?.old_currency }}</span>
      <span class="">Establecido el:</span>
      <span class="text-secondary  mb-3">{{
        catalog_product_company_sale.catalog_product_company?.old_date
      }}</span>

      <span class="">Precio Actual:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company?.new_price }}
        {{ catalog_product_company_sale.catalog_product_company?.new_currency }}</span>
      <span class="">Establecido el:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company?.new_date }}</span>
    </div><br>

    <div class="flex items-center absolute bottom-3 left-4">
      <span class="text-primary">Status:</span>
      <p class="ml-2 px-2 rounded-full border" :class="{
        'border-[#0355B5] text-secondary': getOrderStatus() == 'En proceso',
        'border-green-600 text-green-600': getOrderStatus() == 'Terminado',
        'border-[#9a9a9a] text-[#9a9a9a]': getOrderStatus() == 'Sin iniciar',
      }">{{ getOrderStatus() }}</p>
    </div>

    <div class="absolute bottom-3 right-4">
      <button @click="pauseProduction"
        v-if="catalog_product_company_sale.productions.some(item => item.operator_id == $page.props.auth.user.id) && getNextAction() == 'Finalizar'"
        class="bg-secondary px-2 mr-2 rounded-md text-white disabled:opacity-25 disabled:cursor-not-allowed">
        {{ catalog_product_company_sale.productions.find(item => item.operator_id == $page.props.auth.user.id)?.is_paused
          ? 'Continuar' : 'Pausar' }}
      </button>
      <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
        @confirm="changeTaskStatus">
        <template #reference>
          <button
            v-if="catalog_product_company_sale.productions.some(item => item.operator_id == $page.props.auth.user.id)"
            :disabled="getNextAction() == 'Finalizado'"
            class="bg-secondary px-2 rounded-md text-white disabled:opacity-25 disabled:cursor-not-allowed">
            {{ getNextAction() }}
          </button>
        </template>
      </el-popconfirm>
    </div>
  </div>
  <DialogModal :show="showProgressModal" @close="showProgressModal = false">
    <template #title>
      <h1>Pausar producción</h1>
    </template>
    <template #content>
      <form @submit.prevent="storeProgress()" ref="myForm">
        <div>
          <div class="flex">
            <el-tooltip content="Progreso/avances de producción *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                ...
              </span>
            </el-tooltip>
            <textarea v-model="form.progress" class="textarea mb-1" autocomplete="off"
              placeholder="Progreso/avances de producción *"></textarea>
          </div>
          <InputError :message="form.errors.progress" />
        </div>
        <div class="mt-4">
          <div class="flex">
            <el-tooltip content="Razón de pausa en producción *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                ...
              </span>
            </el-tooltip>
            <textarea v-model="form.pause_justification" class="textarea mb-1" autocomplete="off"
              placeholder="Razón de pausa en producción *"></textarea>
            </div>
            <InputError :message="form.errors.pause_justification" />
        </div>
      </form>
    </template>
    <template #footer>
      <CancelButton @click="showProgressModal = false; form.reset();" :disabled="form.processing">
        Cancelar</CancelButton>
      <PrimaryButton @click="submitForm" :disabled="form.processing">Pausar</PrimaryButton>
    </template>
  </DialogModal>
</template>

<script>
import axios from 'axios';
import moment from "moment";
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      progress: null,
      pause_justification: null,
      production_id: null,
    });

    return {
      form,
      selected: false,
      showProgressModal: false,
    };
  },
  emits: ['selected'],
  props: {
    catalog_product_company_sale: Object,
    is_view_for_seller: {
      type: Boolean,
      default: false
    },
  },
  components: {
    DialogModal,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    IconInput,
    InputError
  },
  methods: {
    submitForm() {
      this.$refs.myForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    pauseProduction() {
      this.form.production_id = this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id)?.id;
      if (this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id)?.is_paused) {
        this.continueProduction();
      } else {
        this.showProgressModal = true;
      }
    },
    async continueProduction() {
      try {
        const response = await axios.put(route('productions.continue-production', this.form.production_id));

        if (response.status === 200) {
          this.$notify({
            title: 'Éxito',
            message: response.data.message,
            type: 'success'
          });

          this.catalog_product_company_sale.productions.find(item => item.id == this.form.production_id).is_paused = 0;
        }
      } catch (error) {
        console.log(error);
      }
    },
    storeProgress() {
      this.form.post(route('production-progress.store'), {
        onSuccess: () => {
          this.$notify({
            title: 'Éxito',
            message: 'Se ha pausado la producción',
            type: 'success'
          });

          this.catalog_product_company_sale.productions.find(item => item.id == this.form.production_id).is_paused = 1;
          this.form.reset();
          this.showProgressModal = false;
        }
      });
    },
    handleSelection() {
      this.selected = !this.selected;
      this.$emit('selected', this.selected);
    },
    getOrderStatus() {
      const productions = this.catalog_product_company_sale.productions;
      const allTasksFinished = productions.every((order) => order.finished_at !== null);
      const someTasksStarted = productions.some((order) => order.started_at !== null && order.finished_at === null);
      const allTasksNotStarted = productions.every((order) => order.started_at === null);

      if (!productions.length) return "Sin iniciar";

      if (allTasksFinished) {
        return "Terminado";
      } else if (someTasksStarted) {
        return "En proceso";
      } else if (allTasksNotStarted) {
        return "Sin iniciar";
      } else {
        return "En proceso";
      }
    },
    getNextAction() {
      const task = this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id);
      if (task.finished_at) return 'Finalizado';
      else if (task.started_at) return 'Finalizar';
      else return 'Iniciar';
    },
    getDateFormtted(dateTime) {
      if (!dateTime) return null;
      return moment(dateTime).format("DD MMM YYYY, hh:mmA");
    },
    async changeTaskStatus() {
      try {
        let task = this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id);
        const response = await axios.put(route('productions.change-status', task.id));

        if (response.status === 200) {
          this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id).started_at = response.data.item.started_at;
          this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id).finished_at = response.data.item.finished_at;

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
    }
  }
};
</script>