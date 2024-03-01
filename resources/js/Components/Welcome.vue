<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="my-5 flex flex-col space-y-3 border-amber-500">
            <MeetingCard />
            <DashboardCard />
            <ProductionPerformanceCard />
            <BirthdateCard />
            <RecentlyAddedCard />
            <InformationCard>
             <template v-slot:title>
               Aniversarios 
            </template>
             <template v-slot:icon>
               <i class="fa-solid fa-champagne-glasses ml-2 border-green-500 text-[#000620]"></i>
            </template>
             <template v-slot:subtitle>
               ¡Gracias por un año más de compromiso en la familia Emblems 3D!
            </template>
             <template v-slot:image>
               <img class="h-32 w-32" src="@/../../public/images/aniversario.png" alt="">
            </template>
            </InformationCard>
            <PrimaryButton>Boton pimario</PrimaryButton>
            <SecondaryButton>Boton secundario</SecondaryButton>
        </div>

            <ApplicationLogo class="block h-12 w-auto" />

        </div>

    </div>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import MeetingCard from '@/Components/MyComponents/MeetingCard.vue';
import DashboardCard from '@/Components/MyComponents/DashboardCard.vue';
import ProductionPerformanceCard from '@/Components/MyComponents/ProductionPerformanceCard.vue';
import BirthdateCard from '@/Components/MyComponents/BirthdateCard.vue';
import RecentlyAddedCard from '@/Components/MyComponents/RecentlyAddedCard.vue';
import InformationCard from '@/Components/MyComponents/InformationCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

export default {
    data(){
        return{

        }
    }, 
    components:{
        ApplicationLogo,
        MeetingCard,
        DashboardCard,
        PrimaryButton,
        SecondaryButton,
        ProductionPerformanceCard,
        InformationCard,
        RecentlyAddedCard,
        BirthdateCard,
    }
}
</script>
