<template>
	<div>
		<NcModal v-if="open"
			:name="t('biblio', 'Add new collection')"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ t('biblio', 'Add new collection') }}</h2>

				<NcTextField v-focus :label="t('biblio', 'Name')" :value.sync="name" @keydown.enter.prevent="submit" />

				<NcButton :disabled="!name"
					type="primary"
					style="margin-top: 15px;"
					@click="submit">
					Submit
					<template #icon>
						<Plus v-if="!loading" :size="20" />
						<NcLoadingIcon v-else />
					</template>
				</NcButton>
			</div>
		</NcModal>
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcModal from "@nextcloud/vue/dist/Components/NcModal.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon.js";

import Plus from "vue-material-design-icons/Plus.vue";

import { useBiblioStore } from "../../store/biblio.js";

export default {
	components: {
		NcButton,
		NcModal,
		NcTextField,
		NcLoadingIcon,
		Plus,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			loading: false,
			name: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore),
	},
	methods: {
		closeModal() {
			this.$emit("update:open", false);
		},
		async submit() {
			if (this.name) {
				this.loading = true;
				try {
					await this.biblioStore.createCollection({ name: this.name });
			        await this.biblioStore.fetchCollections();
				} catch {

				} finally {
					this.loading = false;
					this.closeModal();
					this.name = "";
				}
			}
		},
	},
};
</script>
<style scoped>
.modal__content {
	margin: 50px;
}

.modal__content h2 {
	text-align: center;
}
</style>
