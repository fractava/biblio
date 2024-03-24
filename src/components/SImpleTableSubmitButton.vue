<template>
	<div class="container">
		<NcButton class="submitButton"
			alignment="end-reverse"
			@click="click">
			{{ t("biblio", "Submit") }}
			<template #icon>
				<ArrowRight v-if="!loading" :size="20" />
				<NcLoadingIcon v-else />
			</template>
		</NcButton>
	</div>
</template>

<script>
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon.js";

import ArrowRight from "vue-material-design-icons/ArrowRight.vue";

export default {
	components: {
		NcButton,
		NcLoadingIcon,
		ArrowRight,
	},
	props: {
		loading: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		click(event) {
			this.$emit("click", event);
		},
	},
};
</script>

<style scoped>
.container {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    padding: 8px 7px;

    .submitButton {
        height: 48px;
        margin: 3px 3px 3px 0;
    }
}
</style>
