<template>
	<div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useStore } from "vuex";

const store = useStore();

const medium = ref(store.getters.getMediumById(this.$route.params.id));

onMounted(() => {
  console.log(medium);
})
</script>
