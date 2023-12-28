<template>
    <div class="flex bg-white rounded-md p-1 gap-2">
        <p class="flex-1">{{track.name}}</p>
        <p v-show="secondsSpentCurrent > 0">Spent time: {{timeSpentReadable}} </p>
        <PrimaryButton v-show="!startedAt" @click="startThisTrack"> Start </PrimaryButton>
        <DangerButton v-show="startedAt" @click="stopThisTrack">Stop</DangerButton>
        <p>{{trackManipulationError}}</p>
    </div>
</template>

<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {secondsReadable} from "@/Utils/timeUtils.js";
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
const trackSaveError = ref('')
const secondsSpentCurrent = ref(calculateSpentCurrent())
let refreshSpentSecondsInterval = null
const timeSpentReadable = computed(() => secondsReadable(secondsSpentCurrent.value))
const form = useForm({
    seconds: props.track.seconds
})


function computeIsSomeTrackActiveFromStorage() {
    return read(localStorageKeys.track) && !!read(localStorageKeys.track).startedAt
}

function isCurrentTrackActiveFromStorage() {
    return computeIsSomeTrackActiveFromStorage() && read(localStorageKeys.track).id === props.track.id
}

function startThisTrack () {
    const now = moment.now()

    if (!writeIfDoesntExist(localStorageKeys.track, {id: props.track.id, startedAt: now})) {
        trackManipulationError.value = 'Some track already active. Stop it first'

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
            //todo: test
            trackSaveError.value = 'Error from server, track is not saved, please, try again.'
        }
    })

    if (trackSaveError.value) {
        return
    }

    remove(localStorageKeys.track)
    clearInterval(refreshSpentSecondsInterval)
    startedAt.value = null
}

function getSecondsGoneToNow (startedAtMilliseconds) {
    const started = moment(startedAtMilliseconds);
    const stopped = moment(moment.now());

    return Math.round(stopped.diff(started) / 1000)
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
