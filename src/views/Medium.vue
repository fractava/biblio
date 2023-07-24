<template>
	<div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useMediumsStore } from "../store/mediums";


const store = useMediumsStore();

const medium = ref(store.getters.getMediumById(this.$route.params.id));

onMounted(() => {
  console.log(medium);
})
</script>
