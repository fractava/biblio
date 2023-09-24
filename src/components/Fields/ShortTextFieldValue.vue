<template>
	<div class="field__content">
		<NcInputField ref="input"
			:placeholder="t('biblio', 'Value')"
			:disabled="!allowValueEdit"
			:value="value"
			minlength="0"
			type="text"
			@update:value="onInput"
			@keydown.enter.exact.prevent="onKeydownEnter" />
	</div>
</template>

<script>
import NcInputField from "@nextcloud/vue/dist/Components/NcInputField.js";

export default {
	components: {
		NcInputField,
	},
	methods: {
		onInput() {
			const input = this.$refs.input;
			this.$emit("update:value", input.value);
		},
	},
};
</script>
