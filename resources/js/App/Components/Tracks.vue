<script setup>
import TextInput from "@/Components/TextInput.vue";
import {router} from "@inertiajs/vue3";
import SuccessButton from "@/Components/SuccessButton.vue";
import Track from "@/App/Components/Track.vue";
import {routes} from "@/settings.js";
import {ref} from "vue";

const props = defineProps({
    projectId: {
        type: Number,
        required: true
    },
    tracks: {
        type: Array,
        required: false
    }
})

const name = ref('')
const seconds = ref(0)

function sendPostRequest() {
    router.post(route(routes.tracks_store, {project_id: props.projectId}), {
        name: name.value,
        seconds: seconds.value,
    }, {
      preserveScroll: true
    })
}

</script>

<template>
    <div class="flex flex-col bg-gray-300 p-4 rounded-md gap-4">
        <div class="flex gap-2">
            <TextInput class="flex-1" v-model="name"/>
            <SuccessButton @click="sendPostRequest">Add track</SuccessButton>
        </div>

        <div class="flex flex-col gap-2">
            <Track v-for="track in tracks" :key="track.id" :track="track"/>
        </div>
    </div>
</template>
