
<template>
    <div class="flex flex-col gap-4">
        <div class="flex gap-2">
            <p v-show="!updatingName" class="flex-1 font-semibold">{{project.name}}</p>
            <TextInput
                class="flex-1"
                v-show="updatingName"
                v-model="form.name"
                @input="nameError = ''"
            />
            <InputError :message="nameError"/>
            <SecondaryButton @click="toggleExpand">{{expanded ? 'collapse' : 'expand'}}</SecondaryButton>
        </div>
        <div v-show="expanded" class="flex gap-2 justify-between">
            <SecondaryButton v-show="!updatingName" @click="toggleUpdatingName">Update project name</SecondaryButton>
            <DangerButton v-show="!updatingName" @click="$emit('delete', project.id)">Delete project</DangerButton>
            <div v-show="updatingName" class="flex gap-2">
                <SuccessButton  @click="sendUpdateRequest">Save</SuccessButton>
                <PrimaryButton v-show="updatingName" @click="toggleUpdatingName">Cancel</PrimaryButton>
            </div>
        </div>

        <div v-show="expanded">
            <h3 class="mt-8">Tracks</h3>
            <Tracks
                class="mt-2"
                :tracks="project.tracks"
                :projectId="project.id"
            />
        </div>
    </div>
</template>


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
import SecondaryButton from "@/Components/SecondaryButton.vue";

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
