<template>
	<span>{{ selectedLabel }}</span>
</template>

<script>
import cellMixin from "./cellMixin.js";

export default {
	mixins: [cellMixin],
	props: {
		settings: {
			type: Object,
			default: () => ({}),
		},
	},
	computed: {
		selectedLabel() {
			return this.settings?.options?.find((option) => (option.id === this.value))?.label || "";
		},
	},
};
</script>
