<template>
    <div class="bg-white rounded-md p-1 gap-2">
        <div class="grid grid-cols-2 gap-2 rounded-md p-1">
            <p v-show="!isUpdating" class="flex-1 sm:max-w-40">{{task.name}}</p>
            <textarea class="flex-1" v-show="isUpdating" v-model="form.name"/>
            <div class="min-w-28"><p class="sm:min-w-14" v-show="secondsSpentCurrent > 0">{{timeSpentReadable}} </p></div>
            <SecondaryButton v-show="!startedAt && secondsSpentCurrent === 0 && !isUpdating" @click="startThisTask"> Start </SecondaryButton>
            <SecondaryButton v-show="!startedAt && secondsSpentCurrent > 0 && !isUpdating" @click="startThisTask"> Continue </SecondaryButton>
            <SuccessButton v-show="startedAt" @click="stopThisTask">Stop</SuccessButton>
            <SecondaryButton v-show="!isUpdating" @click="isUpdating = true"> Update </SecondaryButton>
        </div>

        <div class="flex gap-1 ml-1 justify-between" v-show="isUpdating">
            <div class="flex gap-2">
                <SuccessButton @click="sendUpdateTaskRequest">Save</SuccessButton>
                <PrimaryButton @click="cancelUpdate">Cancel</PrimaryButton>
            </div>
            <DangerButton @click="sendDeleteRequest">Delete</DangerButton>
        </div>
        <p class="text-red-600">{{taskError}}</p>
    </div>
</template>

<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {getSecondsGoneToNow, secondsReadable} from "@/Utils/timeUtils.js";
import {computed, onMounted, onUnmounted, ref} from "vue";
import {localStorageKeys, routes} from "@/settings.js";
import {router, useForm} from "@inertiajs/vue3";
import moment from "moment";
import {read, remove, writeIfDoesntExist} from "@/Utils/localStorageUtils.js";
import SuccessButton from "@/Components/SuccessButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
})

const startedAt = ref(null)
const taskError = ref('')
const secondsSpentCurrent = ref(calculateSpentCurrent())
let syncWithStorageInterval = null
const form = useForm({
    name: props.task.name,
    seconds: props.task.seconds
})
const isUpdating = ref(false)
const timeSpentReadable = computed(() => secondsReadable(secondsSpentCurrent.value))

function isSomeTaskActiveFromStorage() {
    return read(localStorageKeys.task) && !!read(localStorageKeys.task).startedAt
}

function isCurrentTaskActiveFromStorage() {
    return isSomeTaskActiveFromStorage() && read(localStorageKeys.task).id === props.task.id
}

function cancelUpdate() {
    isUpdating.value = false

    form.name = props.task.name
}

function sendDeleteRequest() {
    router.delete(route(routes.tasks_delete, {id: props.task.id}), {
        preserveScroll: true,
    })
}

function sendUpdateTaskRequest() {
    form.patch(route(routes.tasks_update, {id: props.task.id}), {
        preserveScroll: true,
        onError: () => {
            flashTaskErrorMessage('Error from server, task is not saved, please, try again.')
        },
        onSuccess: () => {
            isUpdating.value = false
        }
    })
}

function startThisTask () {
    const now = moment.now()

    if (!writeIfDoesntExist(localStorageKeys.task, {id: props.task.id, startedAt: now})) {
        flashTaskErrorMessage('Some task is already in process. Stop it first')

        return
    }

    startedAt.value = now

    setSyncStateWithStorageInterval()
}

function stopThisTask () {
    form.seconds = calculateSpentCurrent()

    sendUpdateTaskRequest()

    remove(localStorageKeys.task)
    clearInterval(syncWithStorageInterval)
    startedAt.value = null
}

function flashTaskErrorMessage(message) {
    taskError.value = message

    setTimeout(() => {
        taskError.value = ''
    }, 3000)
}

function setSyncStateWithStorageInterval() {
    syncWithStorageInterval = setInterval(
        () => {
            loadStateFromStorage()
        },
        1000
    )
}

function loadStateFromStorage() {
    if (!isCurrentTaskActiveFromStorage()) {
        startedAt.value = null
    }

    secondsSpentCurrent.value = calculateSpentCurrent()
}

function calculateSpentCurrent() {
    if (!startedAt.value) {
        return props.task.seconds
    }

    return props.task.seconds + getSecondsGoneToNow(startedAt.value)
}

onMounted(() => {
    if (isCurrentTaskActiveFromStorage()) {
        startedAt.value = read(localStorageKeys.task).startedAt

        setSyncStateWithStorageInterval()
    }
})

onUnmounted(() => {
    remove(localStorageKeys.task)
    clearInterval(syncWithStorageInterval)
})

</script>
