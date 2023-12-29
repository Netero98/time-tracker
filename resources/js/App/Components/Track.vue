<template>
    <div class="bg-white rounded-md p-1 gap-2">
        <div class="grid grid-cols-2 gap-2 rounded-md p-1">
            <p v-show="!isUpdating" class="flex-1 sm:max-w-40">{{track.name}}</p>
            <textarea class="flex-1" v-show="isUpdating" v-model="form.name"/>
            <div class="min-w-28"><p class="sm:min-w-14" v-show="secondsSpentCurrent > 0">{{timeSpentReadable}} </p></div>
            <SecondaryButton v-show="!startedAt && secondsSpentCurrent === 0 && !isUpdating" @click="startThisTrack"> Start </SecondaryButton>
            <SecondaryButton v-show="!startedAt && secondsSpentCurrent > 0 && !isUpdating" @click="startThisTrack"> Continue </SecondaryButton>
            <SuccessButton v-show="startedAt" @click="stopThisTrack">Stop</SuccessButton>
            <SecondaryButton v-show="!isUpdating" @click="isUpdating = true"> Update </SecondaryButton>
        </div>

        <div class="flex gap-1 ml-1 justify-between" v-show="isUpdating">
            <div class="flex gap-2">
                <SuccessButton @click="sendUpdateTrackRequest">Save</SuccessButton>
                <PrimaryButton @click="cancelUpdate">Cancel</PrimaryButton>
            </div>
            <DangerButton @click="sendDeleteRequest">Delete</DangerButton>
        </div>
        <p class="text-red-600">{{trackError}}</p>
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
    track: {
        type: Object,
        required: true
    }
})

const startedAt = ref(null)
const trackError = ref('')
const secondsSpentCurrent = ref(calculateSpentCurrent())
let refreshSpentSecondsInterval = null
const form = useForm({
    name: props.track.name,
    seconds: props.track.seconds
})
const isUpdating = ref(false)
const timeSpentReadable = computed(() => secondsReadable(secondsSpentCurrent.value))

function isSomeTrackActiveFromStorage() {
    return read(localStorageKeys.track) && !!read(localStorageKeys.track).startedAt
}

function isCurrentTrackActiveFromStorage() {
    return isSomeTrackActiveFromStorage() && read(localStorageKeys.track).id === props.track.id
}

function cancelUpdate() {
    isUpdating.value = false

    form.name = props.track.name
}

function sendDeleteRequest() {
    router.delete(route(routes.tracks_delete, {id: props.track.id}), {
        preserveScroll: true,
    })
}

function sendUpdateTrackRequest() {
    form.patch(route(routes.tracks_update, {id: props.track.id}), {
        preserveScroll: true,
        onError: () => {
            flashTrackErrorMessage('Error from server, track is not saved, please, try again.')
        },
        onSuccess: () => {
            isUpdating.value = false
        }
    })
}

function startThisTrack () {
    const now = moment.now()

    if (!writeIfDoesntExist(localStorageKeys.track, {id: props.track.id, startedAt: now})) {
        flashTrackErrorMessage('Some track is already active. Stop it first')

        return
    }

    startedAt.value = now

    setRefreshSpentSecondsInterval()
}

function stopThisTrack () {
    form.seconds = calculateSpentCurrent()

    sendUpdateTrackRequest()

    remove(localStorageKeys.track)
    clearInterval(refreshSpentSecondsInterval)
    startedAt.value = null
}

function flashTrackErrorMessage(message) {
    trackError.value = message

    setTimeout(() => {
        trackError.value = ''
    }, 3000)
}

function setRefreshSpentSecondsInterval() {
    refreshSpentSecondsInterval = setInterval(
        () => {
            secondsSpentCurrent.value = calculateSpentCurrent()
        },
        1000
    )
}

function calculateSpentCurrent() {
    if (!startedAt.value) {
        return props.track.seconds
    }

    return props.track.seconds + getSecondsGoneToNow(startedAt.value)
}

onMounted(() => {
    if (isCurrentTrackActiveFromStorage()) {
        startedAt.value = read(localStorageKeys.track).startedAt

        setRefreshSpentSecondsInterval()
    }
})

onUnmounted(() => {
    remove(localStorageKeys.track)
    clearInterval(refreshSpentSecondsInterval)
})

</script>
