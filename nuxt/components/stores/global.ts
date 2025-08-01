import {defineStore} from "pinia";
import {ref} from "vue";

export const useGlobalStore = defineStore('global', () => {
    const route = useRoute();
    const navMenu = ref(false);
    const title = reactive([]);
    const loading = ref(false);
    const breadcrumbs = ref(false);
    const breadcrumbsLoading = ref(false);
    const breadcrumbsTree = ref([]);

    function breadcrumbsReset(){
        breadcrumbs.value = false;
        breadcrumbsTree.value = [];
        breadcrumbsLoading.value = true;
    }

    return {
        navMenu,
        title,
        loading,
        breadcrumbs,
        breadcrumbsTree,
        breadcrumbsLoading,

        breadcrumbsReset,
    }
})
