import {defineStore} from "pinia";
import type {ref} from "vue";

const defaultParams = {
    timeout: 3000,
    variant: 'success',
    text: '',
    title: '',
}

export const useSnackbarStore = defineStore('snackbar', () => {

    const snackbars = ref([])

    function addSnackbar(params){

        snackbars.value.push({
            ...defaultParams,
            ...params,
        })
    }

    return {
        defaultParams,
        snackbars,

        addSnackbar
    }
})
