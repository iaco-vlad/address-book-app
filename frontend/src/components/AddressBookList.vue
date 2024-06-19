<template>
    <div class="list">
        <div class="header">
            <h2>Address Book</h2>
            <b-button variant="primary" @click="navigateToGroups">Groups</b-button>

            <div class="actions">
                <b-form-input v-model="searchTerm" placeholder="Search..." class="search-input"></b-form-input>
                <b-button variant="primary" @click="navigateToCreate">Create New</b-button>

                <a :href="jsonExportUrl" target="_blank" class="btn btn-primary mx-2">Export JSON</a>
                <a :href="xmlExportUrl" target="_blank" class="btn btn-primary">Export XML</a>
            </div>
        </div>

        <div class="filters">
            <b-form-select v-model="selectedTag" :options="tagOptions" @change="applyTagFilter">
                <template #first>
                    <option value="">All Tags</option>
                </template>
            </b-form-select>
        </div>

        <b-table striped hover :items="addresses" :fields="fields">
            <template #cell(actions)="row">
                <b-button variant="primary" size="sm" @click="navigateToEdit(row.item.id)">Edit</b-button>
                <b-button variant="danger" size="sm" @click="confirmDelete(row.item.id)">Delete</b-button>
            </template>
        </b-table>

        <b-pagination
            v-model="currentPage"
            :total-rows="pagination.total"
            :per-page="pagination.per_page"
        />

        <div v-if="showDeleteModal" class="delete-modal">
            <div class="delete-modal-content">
                <h3>Confirmation</h3>
                <p v-if="idToDelete">Are you sure you want to delete this address?</p>
                <p v-else>Are you sure you want to delete the selected addresses?</p>
                <div class="delete-modal-actions">
                    <button @click="deleteConfirmed()" class="btn-confirm">OK</button>
                    <button @click="cancelDelete" class="btn-cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { BFormCheckbox, BFormInput, BButton, BTable, BPagination, BFormSelect } from 'bootstrap-vue-next';

export default {
    name: 'AddressBookList',
    components: {
        BFormCheckbox,
        BFormInput,
        BButton,
        BFormSelect,
        BTable,
        BPagination,
    },
    data() {
        return {
            addresses: [],
            searchTerm: '',
            fields: [
                { key: 'last_name', label: 'Last Name' },
                { key: 'first_name', label: 'First Name' },
                { key: 'email', label: 'Email' },
                { key: 'street', label: 'Street' },
                { key: 'zip_code', label: 'Zip Code' },
                { key: 'city_name', label: 'City' },
                { key: 'actions', label: 'Actions' },
            ],
            pagination: {},
            currentPage: 1,
            idToDelete: null,
            showDeleteModal: false,
            selectedIds: [],
            tags: [],
            selectedTag: '',
        };
    },
    computed: {
        jsonExportUrl() {
            return this.$http.baseUrl + '/addresses/export/json';
        },
        xmlExportUrl() {
            return this.$http.baseUrl + '/addresses/export/xml';
        },
        tagOptions() {
            return [
                ...this.tags.map(tag => ({ value: tag.id, text: tag.name })),
            ];
        },
    },
    methods: {
        async fetchAddresses(page = null) {
            if (page === null) {
                page = this.currentPage ?? 1;
            }
            try {
                const response = await this.$http.get(`/addresses?page=${page}&search=${this.searchTerm}&tag=${this.selectedTag}`);
                this.addresses = response.data.data;
                this.pagination = response.data.meta;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchTags() {
            try {
                const response = await this.$http.get('/tags/active');
                this.tags = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        applyTagFilter() {
            this.currentPage = 1;
            this.fetchAddresses();
        },
        navigateToGroups() {
            this.$router.push({ name: 'group-list' });
        },
        navigateToCreate() {
            this.$router.push({ name: 'address-book-create' });
        },
        navigateToEdit(id) {
            this.$router.push({ name: 'address-book-edit', params: { id } });
        },
        async confirmDelete(id) {
            this.idToDelete = id;
        },
        async deleteConfirmed() {
            try {
                this.$http.delete(`/addresses/${this.idToDelete}`)
                    .then(() => {
                        this.fetchAddresses();
                    });
                this.showDeleteModal = false;
                this.idToDelete = null;
            } catch (error) {
                console.error(error);
            }
        },
        cancelDelete() {
            this.showDeleteModal = false;
            this.idToDelete = null;
        },
    },
    watch: {
        currentPage() {
            this.fetchAddresses(this.currentPage);
        },
        searchTerm() {
            this.fetchAddresses();
        },
        idToDelete(newValue) {
            if (newValue !== null) {
                this.showDeleteModal = true;
            }
        },
    },
    async mounted() {
        await this.fetchAddresses();
        await this.fetchTags();
    },
};
</script>
