<template>
  <AppLayoutNoHeader title="Gestor de proyectos |">
    <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Proyectos</span>
    </div>

    <div class="flex justify-between mt-5 mx-14">
        <div class="md:w-1/3 mr-2">
          <el-select v-model="selectedProyect" clearable filterable placeholder="Buscar proyecto"
            no-data-text="No hay proyectos registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in projects?.data" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </div>
        <div>
          <PrimaryButton @click="$inertia.get(route('projects.create'))">Nuevo proyecto</PrimaryButton>
        </div>
      </div>

    <div class="px-16 py-7 text-sm">
      <table class="w-full">
            <thead>
            <tr class="text-left">
                <th class="font-normal pb-5">Nombre del proycto <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-normal pb-5">Estado <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-normal pb-5">Tareas <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-normal pb-5">Fecha de inicio <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-normal pb-5">Fecha final <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-normal pb-5">Completa <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(project, index) in projects?.data" :key="project.id" class="mb-4">
                <td class="text-left pb-3">
                {{ 'role.id '}}
                </td>
                <td class="text-left pb-3">
                <span class="hover:underline cursor-pointer">{{ 'role.name '}}</span>
                </td>
                <td class="text-left pb-3">
                {{ 'role.created_at' }}
                </td>
                <td class="text-left pb-3">
                {{ 'role.created_at' }}
                </td>
                <td class="text-left pb-3">
                {{ 'role.created_at' }}
                </td>
                <td class="text-left pb-3">
                {{ 'role.created_at' }}
                </td>
                <td class="text-left pb-3">
                <div>
                    <!-- <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar roles y permisos')"
                    confirm-button-text="Si" cancel-button-text="No" icon-color="#FFFFFF" title="¿Continuar?"
                    @confirm="deleteRole(role, index)">
                    <template #reference>
                        <i class="fa-solid fa-trash-can text-red-600 cursor-pointer"></i>
                    </template>
                    </el-popconfirm> -->
                </div>
                </td>
            </tr>
            </tbody>
        </table>
</div>

  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    data(){
        return{
            selectedProyect: "",
        }
    },
    components:{
        AppLayoutNoHeader,
        PrimaryButton
    },
    props:{

    },
    methods:{

    },
}
</script>
