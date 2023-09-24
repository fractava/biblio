<template>
	<div class="field__content">
		<textarea ref="textarea"
			:placeholder="fieldType.valuePlaceholder"
			:disabled="!allowValueEdit"
			:value="value"
			class="field__text"
			minlength="1"
			@input="onInput"
			@keypress="autoSizeText"
			@keydown.ctrl.enter="onKeydownCtrlEnter" />
	</div>
</template>

<script>

export default {
	data() {
		return {
			height: 1,
		};
	},

	mounted() {
		this.autoSizeText();
	},

	methods: {
		onInput() {
			const textarea = this.$refs.textarea;
			this.$emit("update:value", textarea.value);
			this.autoSizeText();
		},
		autoSizeText() {
			const textarea = this.$refs.textarea;
			textarea.style.cssText = "height:auto; padding:0";
			textarea.style.cssText = `height: ${textarea.scrollHeight + 20}px`;
		},
		onKeydownCtrlEnter(event) {
			this.$emit("keydown", event);
		},
	},
}
</script>

<style lang="scss" scoped>
.field__text {
	// make sure height calculations are correct
	box-sizing: content-box !important;
	width: 100%;
	min-width: 100%;
	max-width: 100%;
	min-height: 44px;
	margin: 0;
	padding: 6px 0;
	border: 0;
	border-bottom: 1px dotted var(--color-border-dark);
	border-radius: 0;
	resize: none;
	font-size: 14px;

	&:disabled {
		// Just overrides Server CSS-Styling for disabled inputs. -> Not Good??
		background-color: var(--color-main-background);
		color: var(--color-main-text);
	}
}

</style>
