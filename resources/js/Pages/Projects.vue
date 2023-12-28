<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {routes} from "@/settings.js";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {ref} from "vue";
import Project from "@/App/Components/Project.vue";
import InputError from "@/Components/InputError.vue";
import SuccessButton from "@/Components/SuccessButton.vue";

defineProps({
    projects: {
        type: Array,
        required: false
    }
})

const confirmingProjectDeletion = ref(false);
const nameError = ref('')
const form = useForm({
    id: '',
    name: '',
})

const closeModal = () => {
    confirmingProjectDeletion.value = false;

    form.reset();
};

const sendCreateRequest = () => {
    form.post(route(routes.projects_store), {
        onSuccess: () => form.reset(),
        onError: (errors) => nameError.value = errors.name,
    })
}

const sendDeleteRequest = () => {
    form.delete(route(routes.projects_destroy, { id: form.id }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    });
};

const setPreDeleteState = (id) => {
    form.id = id

    confirmingProjectDeletion.value = true
}

</script>

<template>
    <AuthenticatedLayout>
        <div class="mt-10 max-w-2xl mx-auto p-6">
            <div class="flex mx-auto gap-2">
                <TextInput @input="nameError = ''" class="flex-1" v-model="form.name"></TextInput>
                <SuccessButton @click="sendCreateRequest">Add project</SuccessButton>
                <InputError :message="nameError"/>
            </div>

            <div class="mt-4 flex flex-col gap-4">
                <div v-for="project in projects" class="p-4 bg-white rounded-md">
                    <Project
                        @delete="(id) => { setPreDeleteState(id) }"
                        :project="project"
                        :key="project.id"
                    />
                </div>
            </div>

            <Modal :show="confirmingProjectDeletion" @close="closeModal">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Are you sure you want to delete this project?
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Once project is deleted, all of its related data will be permanently deleted.
                    </p>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                        <DangerButton
                            class="ms-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="sendDeleteRequest"
                        >
                            Delete Project
                        </DangerButton>
                    </div>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
