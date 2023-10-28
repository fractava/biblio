<template>
	<div class="nomenclatureOption">
		<component :is="option.icon" v-if="option.icon" />
		{{ option.label }}
	</div>
</template>
<script>
export default {
	props: {
		option: {
			type: Object,
			default: () => ({}),
		},
	},
};
</script>
<style>
.nomenclatureOption {
    display: flex;
    gap: 10px;
}
</style>
