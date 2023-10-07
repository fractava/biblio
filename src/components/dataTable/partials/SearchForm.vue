<template>
	<div :class="{ empty: localValue === '' }">
		<NcTextField :value.sync="localValue"
			:label="t('biblio', 'Search')"
			trailing-button-icon="close"
			:show-trailing-button="localValue !== ''"
			@trailing-button-click="clearValue"
			@update:value="submit">
			<Magnify :size="16" />
		</NcTextField>
	</div>
</template>

<script>
import { NcTextField } from "@nextcloud/vue";
import Magnify from "vue-material-design-icons/Magnify.vue";

export default {

	components: {
		Magnify,
		NcTextField,
	},

	props: {
		searchString: {
			type: String,
			default: null,
		},
	},

	data() {
		return {
			localValue: "",
		};
	},

	methods: {
		clearValue() {
			this.localValue = "";
			this.submit();
		},
		submit() {
			this.$emit("set-search-string", this.localValue);
		},
	},

};
</script>
<style scoped>

:deep(.input-field input) {
	border-color: var(--color-primary-element);
}

.empty :deep(.input-field input) {
	border-color: transparent;
}

</style>
