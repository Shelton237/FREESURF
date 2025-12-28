<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
  canResetPassword: { type: Boolean },
  status: { type: String },
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const show = ref(false)

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Connexion" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <InputLabel for="email" value="E-mail" />

        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autofocus
          autocomplete="username"
        />

        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div>
        <InputLabel for="password" value="Mot de passe" />
        <div class="relative">
          <TextInput :type="show ? 'text' : 'password'" id="password" class="mt-1 block w-full pr-20" v-model="form.password" required autocomplete="current-password" />
          <button type="button" class="absolute inset-y-0 right-0 mt-1 mr-2 px-2 text-sm text-gray-600 hover:text-brand" @click="show = !show">{{ show ? 'Masquer' : 'Afficher' }}</button>
        </div>
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4 block">
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Se souvenir de moi</span>
        </label>
      </div>

      <div class="mt-4 flex items-center justify-between">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-brand hover:underline"
        >
          Mot de passe oublié ?
        </Link>

        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Se connecter</PrimaryButton>
      </div>
    </form>
  </GuestLayout>
  
</template>






