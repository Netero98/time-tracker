<template>
    <div class="bg-white rounded-md p-1 gap-2">
        <div class="flex rounded-md p-1 gap-2">
            <p class="flex-1 overflow-hidden">{{track.name}}</p>
            <p v-show="secondsSpentCurrent > 0">{{timeSpentReadable}} </p>
            <PrimaryButton v-show="!startedAt && secondsSpentCurrent === 0" @click="startThisTrack"> Start </PrimaryButton>
            <PrimaryButton v-show="!startedAt && secondsSpentCurrent > 0" @click="startThisTrack"> Continue </PrimaryButton>
            <DangerButton v-show="startedAt" @click="stopThisTrack">Stop</DangerButton>
        </div>

        <p class="text-red-600">{{trackManipulationError}}</p>
    </div>
</template>

<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {getSecondsGoneToNow, secondsReadable} from "@/Utils/timeUtils.js";
import {computed, onMounted, onUnmounted, ref} from "vue";
import {localStorageKeys, routes} from "@/settings.js";
import DangerButton from "@/Components/DangerButton.vue";
import {useForm} from "@inertiajs/vue3";
import moment from "moment";
import {read, remove, writeIfDoesntExist} from "@/Utils/localStorageUtils.js";

const props = defineProps({
    track: {
        type: Object,
        required: true
    }
})

const startedAt = ref(null)
const trackManipulationError = ref('')
const secondsSpentCurrent = ref(calculateSpentCurrent())
let refreshSpentSecondsInterval = null
const form = useForm({
    seconds: props.track.seconds
})
const timeSpentReadable = computed(() => secondsReadable(secondsSpentCurrent.value))

function isSomeTrackActiveFromStorage() {
    return read(localStorageKeys.track) && !!read(localStorageKeys.track).startedAt
}

function isCurrentTrackActiveFromStorage() {
    return isSomeTrackActiveFromStorage() && read(localStorageKeys.track).id === props.track.id
}

function startThisTrack () {
    const now = moment.now()

    if (!writeIfDoesntExist(localStorageKeys.track, {id: props.track.id, startedAt: now})) {
        flashErrorMessage('Some track is already active. Stop it first')

        return
    }

    startedAt.value = now

    setRefreshSpentSecondsInterval()
}

function stopThisTrack () {
    form.seconds = calculateSpentCurrent()

    form.patch(route(routes.tracks_update, {id: props.track.id}), {
        preserveScroll: true,
        onError: () => {
            flashErrorMessage('Error from server, track is not saved, please, try again.')
        }
    })

    remove(localStorageKeys.track)
    clearInterval(refreshSpentSecondsInterval)
    startedAt.value = null
}

function flashErrorMessage(message) {
    trackManipulationError.value = message

    setTimeout(() => {
        trackManipulationError.value = ''
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
