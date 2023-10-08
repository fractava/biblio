<template>
	<div>
		<span>{{ limitedValue }}</span>
	</div>
</template>

<script>
import cellMixin from "./cellMixin";

export default {
	mixins: [cellMixin],
	computed: {
		limitedValue() {
			if (this.value.length > 30) {
				return this.value.slice(0, 27) + "...";
			} else {
				return this.value;
			}
		},
	},
};
</script>
