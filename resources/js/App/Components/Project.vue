<script setup>

import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {ref} from "vue";
import SuccessButton from "@/Components/SuccessButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {routes} from "@/settings.js";
import InputError from "@/Components/InputError.vue";
import Tracks from "@/App/Components/Tracks.vue";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    }
})

defineEmits(['delete'])

const expanded = ref(false)
const updatingName = ref(false)
const nameError = ref('')
const form = useForm({
    name: props.project.name
})

const toggleExpand = () => {
    updatingName.value = false
    expanded.value = !expanded.value
}

const toggleUpdatingName = () => {
    updatingName.value = !updatingName.value
}

const sendUpdateRequest = () => {
    form.patch(route(routes.projects_update, { id: props.project.id }), {
        preserveScroll: true,
        onSuccess: () => updatingName.value = false,
        onError: (errors) => nameError.value = errors.name
    })
}

</script>

<template>
    <div class="flex gap-4 flex-col">
        <div class="flex gap-2">
            <p v-show="!updatingName" class="flex-1">{{project.name}}</p>
            <TextInput
                class="flex-1"
                v-show="updatingName"
                v-model="form.name"
                @input="nameError = ''"
            />
            <InputError :message="nameError"/>
            <PrimaryButton @click="toggleExpand">{{expanded ? 'collapse' : 'expand'}}</PrimaryButton>
        </div>
        <div v-show="expanded" class="flex gap-2">
            <SuccessButton v-show="!updatingName" @click="toggleUpdatingName">Update project name</SuccessButton>
            <DangerButton v-show="!updatingName" @click="$emit('delete', project.id)">Delete project</DangerButton>
            <SuccessButton v-show="updatingName" @click="sendUpdateRequest">Save</SuccessButton>
            <DangerButton v-show="updatingName" @click="toggleUpdatingName">Cancel</DangerButton>
        </div>

        <div v-show="expanded">
            <h3 class="mt-8 font-semibold">Tracks</h3>
            <Tracks
                class="mt-2"
                :tracks="project.tracks"
                :projectId="project.id"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
