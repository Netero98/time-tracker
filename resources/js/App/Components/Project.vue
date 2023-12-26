<script setup>

import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {ref} from "vue";
import SuccessButton from "@/Components/SuccessButton.vue";

defineProps({
    project: {
        type: Object,
        required: true,
    }
})

defineEmits(['delete'])

const expanded = ref(false)
const updatingName = ref(false)

const toggleExpand = () => {
    expanded.value = !expanded.value
}

const toggleUpdatingName = () => {
  updatingName.value = !updatingName.value
}

</script>

<template>
    <div class="flex flex-col">
        <div class="flex gap-4">
            <div class="flex-1">{{project.name}}</div>
            <PrimaryButton @click="toggleExpand">{{expanded ? 'collapse' : 'expand'}}</PrimaryButton>
            <DangerButton @click="$emit('delete', project.id)">Delete</DangerButton>
        </div>
        <div v-show="expanded">
          <SuccessButton v-show="!updatingName" @click="toggleUpdatingName">Update name</SuccessButton>
          <SuccessButton v-show="updatingName" @click="toggleUpdatingName">Save</SuccessButton>
        </div>
    </div>
</template>

<style scoped>

</style>
