<script setup>
import TextInput from "@/Components/TextInput.vue";
import {router} from "@inertiajs/vue3";
import SuccessButton from "@/Components/SuccessButton.vue";
import {routes} from "@/settings.js";
import {ref} from "vue";
import Task from "@/App/Components/Task.vue";

const props = defineProps({
    projectId: {
        type: Number,
        required: true
    },
    tasks: {
        type: Array,
        required: false
    }
})

const name = ref('')
const seconds = ref(0)

function sendPostRequest() {
    router.post(route(routes.tasks_store, {project_id: props.projectId}), {
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
            <SuccessButton @click="sendPostRequest">Add task</SuccessButton>
        </div>

        <div class="flex flex-col gap-2">
            <Task v-for="task in tasks" :key="task.id" :task="task"/>
        </div>
    </div>
</template>
