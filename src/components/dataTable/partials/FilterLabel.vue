<template>
	<div class="filter" :class="[type]">
		{{ label + " \"" + operand + "\"" }}
		<button @click="actionDelete">
			<Close :size="14" />
		</button>
	</div>
</template>
<script>
import Close from "vue-material-design-icons/Close.vue";

export default {

	components: {
		Close,
	},

	props: {
		label: {
			type: String,
			default: "",
		},
		operand: {
			type: [String, Number],
			default: "",
		},
		type: {
			type: String,
			default: "highlighted", // highlighted, outlined
		},
		id: {
			type: String,
			default: null,
		},
	},

	computed: {
		getValue() {
			let value = this.value;
			return value;
		},
	},

	methods: {
		actionDelete() {
			this.$emit("delete-filter", this.id);
		},
	},

};
</script>
<style lang="scss" scoped>

.highlighted {
	background-color: var(--color-border-maxcontrast);
	color: var(--color-primary-element-text-dark);
}

.outlined {
	border: 1px solid;
}

.filter {
	padding-top: 1px;
	padding-left: calc(var(--default-grid-baseline) * 2);
	padding-right: calc(var(--default-grid-baseline) * 6);
	padding-bottom: calc(var(--default-grid-baseline) * .5);
	font-size: 0.9em;
	border-radius: var(--border-radius-pill);
	margin-bottom: calc(var(--default-grid-baseline) * 1);
	width: fit-content;
	color: var(--color-primary-element-text-dark);
	line-height: initial;
}

button {
	position: absolute;
	padding-top: 1px;
	min-height: auto;
	padding-bottom: 1px;
	padding-left: 2px;
	padding-right: 2px;
	margin: 0;
	margin-left: 4px;
}

</style>
