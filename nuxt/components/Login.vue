<template>
    <v-card class="pa-6 w-sm-100 w-md-50 w-lg-33 mx-auto mt-10" elevation="6" rounded="lg" :loading="loading">
        <v-card-title class="text-h5 mb-4">Login</v-card-title>
        <v-form v-model="valid" @submit.prevent="handleLogin">
            <v-text-field
                v-model="email"
                label="Email"
                prepend-inner-icon="mdi-email"
                type="email"
                required
                density="comfortable"
                :rules="[
                    v => !!v || 'Email is required',
                    v => /.+@.+\..+/.test(v) || 'Email must be valid',
                ]"
            />
            <v-text-field
                required
                v-model="password"
                label="Password"
                prepend-inner-icon="mdi-lock"
                type="password"
                density="comfortable"
                :rules="[
                    v => !!v || 'Password is required',
                    v => v.length >= 6 || 'Password must be at least 6 characters',
                ]"
            />
            <v-label v-if="error" class="d-flex justify-center text-red-accent-4 font-italic" :text="error" />
            <v-btn
                block
                type="submit"
                color="primary"
                class="mt-4"
                :loading="loading"
                :disabled="!valid || loading"
            >
                Login
            </v-btn>
        </v-form>
    </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const valid = ref(false)
const loading = ref(false)
const router = useRouter()
const error = ref('')

function handleLogin(){
    if (!valid.value) return

    error.value = '';
    loading.value = true;
    fetch('/api/login', {
        method: 'POST',
        body: JSON.stringify({email: email.value, password: password.value}),
    })
    .then(res => {
        if(res.ok) router.push('/dashboard');
        else error.value = JSON.parse(res.statusText)?.message
    })
    .finally(() => loading.value = false)
}
</script>
