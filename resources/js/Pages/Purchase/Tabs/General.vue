<template>
    <div class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
      <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
        <span class="text-gray-500">ID</span>
        <span>{{ purchase.id }}</span>
        <span class="text-gray-500 my-2">Creado por</span>
        <span>{{ purchase.user.name }}</span>
        <span class="text-gray-500 my-2">Proveedor</span>
        <span>{{ purchase.supplier.name }}</span>
        <span class="text-gray-500 my-2">Creado el</span>
        <span>{{ purchase.created_at }}</span>
        <span class="text-gray-500 my-2">Autorizado el</span>
        <span>{{ purchase.authorized_at }}</span>
        <span class="text-gray-500 my-2">Autorizado por</span>
        <span>{{ purchase.authorized_user_name }}</span>
        <span class="text-gray-500 my-2">Fecha de entrega esperada</span>
        <span>{{ purchase.expected_delivery_date }}</span>
        <span class="text-gray-500 my-2">Fecha de realización de pedido</span>
        <span>{{ purchase.emited_at }}</span>
        <span class="text-gray-500 my-2">Fecha de recibido</span>
        <span>{{ purchase.recieved_at }}</span>
        <span class="text-gray-500 my-2">Moneda</span>
        <span>{{ purchase.currency ?? '-' }}</span>
        <span class="text-gray-500 my-2">Notas</span>
        <span>{{ purchase.notes }}</span>
      </div>
      <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
        <p class="text-secondary col-span-2 mb-2">Datos del proveedor</p>

        <span class="text-gray-500">ID</span>
        <span>{{ purchase.supplier.id }}</span>
        <span class="text-gray-500 my-2">Nombre</span>
        <span>{{ purchase.supplier.name }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ purchase.supplier.address }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ purchase.supplier.post_code }}</span>
        <span class="text-gray-500 my-2">Teléfono</span>
        <span>{{ purchase.supplier.phone }}</span>

        <p class="text-secondary col-span-2 mt-7">Datos Bancarios</p>

        <span class="text-gray-500 my-2">Nombre del beneficiario</span>
        <span>{{ purchase.supplier.banks[purchase.bank_information]?.beneficiary_name ?? '--' }}</span>
        <span class="text-gray-500 my-2">Número de cuenta</span>
        <span>{{ purchase.supplier.banks[purchase.bank_information]?.accountNumber ?? '--' }}</span>
        <span class="text-gray-500 my-2">Clabe</span>
        <span>{{ purchase.supplier.banks[purchase.bank_information]?.clabe ?? '--' }}</span>
        <span class="text-gray-500 my-2">Banco</span>
        <span>{{ purchase.supplier.banks[purchase.bank_information]?.bank_name ?? '--' }}</span>
      </div>
    </div>
</template>
<script>

export default {
    data() {
        return {

        }
    },
    props: {
        purchase: Object,
    },
    components: {

    },
    methods: {
    },
}
</script>