<template>
    <div>
        <div class=" container mx-auto my-4">
         <el-table-v2
            :columns="columns"
            :data="[]"
            :row-height="40"
            :width="700"
            :height="400"
            :footer-height="50"
        >
            <template #empty>
            <div class="flex items-center justify-center h-100%">
                <el-empty />
            </div>
            </template>
        </el-table-v2>
        </div>
    </div>
</template>

<script>
import { reactive } from 'vue';

export default {
    data(){
      
    const generateColumns = (length = 10, prefix = 'column-', props) =>
        Array.from({ length }).map((_, columnIndex) => ({
            ...props,
            key: `${prefix}${columnIndex}`,
            dataKey: `${prefix}${columnIndex}`,
            title: `Column ${columnIndex}`,
            width: 150,
  }));

  const props = reactive({}); // Si necesitas propiedades reactivas, decláralas aquí
    const columns = generateColumns(10, 'column-', props);

    
        return{
             columns,
        }
    }
}
</script>