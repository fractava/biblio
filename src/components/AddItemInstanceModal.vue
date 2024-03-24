<template>
	<div>
		<NcModal v-if="open"
			:name="t('biblio', 'Add new Item Instance')"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ t('biblio', 'Add new Item Instance') }}</h2>

				<ButtonGroup style="margin-bottom: 10px;">
					<NcButton :pressed="!multipleMode"
						@click="multipleMode = false">
						{{ t('biblio', 'Single Mode') }}
					</NcButton>
					<NcButton :pressed="multipleMode"
						@click="multipleMode = true">
						{{ t('biblio', 'Batch Mode') }}
					</NcButton>
				</ButtonGroup>

				<div v-if="!multipleMode">
					<NcTextField :label="t('biblio', 'Barcode')" :value.sync="barcode" />
				</div>
				<div v-else>
					<NcTextField :label="t('biblio', 'Prefix')" :value.sync="prefix" />
					<NcInputField :label="t('biblio', 'Start Number')" type="number" :value.sync="startNumber" />
					<NcInputField :label="t('biblio', 'Count')" type="number" :value.sync="count" />
					<NcTextField :label="t('biblio', 'Suffix')" :value.sync="suffix" />
				</div>

				<NcButton :disabled="!barcode"
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
import NcInputField from "@nextcloud/vue/dist/Components/NcInputField.js"
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon.js";

import Plus from "vue-material-design-icons/Plus.vue";

import ButtonGroup from "./ButtonGroup.vue";
import { useBiblioStore } from "../store/biblio.js";
import { api } from "../api.js";

export default {
	components: {
		NcButton,
		NcModal,
		NcTextField,
		NcInputField,
		NcLoadingIcon,
		Plus,
		ButtonGroup,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
		itemId: {
			type: Number,
			required: true,
		},
		defaultPrefix: {
			type: String,
			default: "",
		},
	},
	data() {
		return {
			multipleMode: false,
			loading: false,
			barcode: "",
			prefix: "",
			startNumber: 1,
			count: 1,
			suffix: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore),
	},
	watch: {
		defaultPrefix() {
			this.resetBarcode();
		},
	},
	mounted() {
		this.resetBarcode();
	},
	methods: {
		closeModal() {
			this.$emit("update:open", false);
			this.resetBarcode();
		},
		resetBarcode() {
			this.barcode = this.defaultPrefix;
			this.prefix = this.defaultPrefix;
		},
		async submit() {
			this.loading = true;
			try {
				const newInstance = await api.createItemInstance(this.$route.params.collectionId, {
					itemId: this.itemId,
					barcode: this.barcode,
				});

				this.$emit("added-instance", newInstance);
			} catch (error) {
				console.error(error);
			} finally {
				this.loading = false;
				this.closeModal();
				this.resetBarcode();
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
