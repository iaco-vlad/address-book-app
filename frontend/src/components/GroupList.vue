<template>
    <div class="list">
        <div class="header">
            <h2>Groups</h2>
            <b-button variant="primary" @click="navigateToAddressBook">Address Book</b-button>

            <div class="actions">
                <b-form-input v-model="searchTerm" placeholder="Search..." class="search-input"></b-form-input>
                <b-button variant="primary" @click="navigateToCreate">Create New</b-button>
            </div>
        </div>
        <b-table striped hover :items="groups" :fields="fields">
            <template #cell(direct_contact_ids)="row">
                {{ row.item.direct_contact_ids.length }}
            </template>
            <template #cell(child_group_ids)="row">
                {{ row.item.child_group_ids.length }}
            </template>
            <template #cell(parent_group_ids)="row">
                {{ row.item.parent_group_ids.length }}
            </template>
            <template #cell(inherited_contact_ids)="row">
                {{ row.item.inherited_contact_ids.length }}
            </template>
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
                <p>Are you sure you want to delete this group?</p>
                <div class="delete-modal-actions">
                    <button @click="deleteConfirmed()" class="btn-confirm">OK</button>
                    <button @click="cancelDelete" class="btn-cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { BFormInput, BButton, BTable, BPagination } from 'bootstrap-vue-next';

export default {
    name: 'GroupList',
    components: {
        BFormInput,
        BButton,
        BTable,
        BPagination,
    },
    data() {
        return {
            groups: [],
            searchTerm: '',
            fields: [
                { key: 'name', label: 'Name' },
                { key: 'direct_contact_ids', label: 'Direct Contacts' },
                { key: 'child_group_ids', label: 'Child Groups' },
                { key: 'parent_group_ids', label: 'Parent Groups' },
                { key: 'inherited_contact_ids', label: 'Inherited Contacts' },
                { key: 'actions', label: 'Actions' },
            ],
            pagination: {},
            currentPage: 1,
            idToDelete: null,
            showDeleteModal: false,
        };
    },
    methods: {
        async fetchGroups(page = null) {
            if (page === null) {
                page = this.currentPage ?? 1;
            }
            try {
                const response = await this.$http.get(`/groups?page=${page}&search=${this.searchTerm}`);
                this.groups = response.data.data;
                this.pagination = response.data.meta;
            } catch (error) {
                console.error(error);
            }
        },
        navigateToAddressBook() {
            this.$router.push({ name: 'address-book-list' });
        },
        navigateToCreate() {
            this.$router.push({ name: 'group-create' });
        },
        navigateToEdit(id) {
            this.$router.push({ name: 'group-edit', params: { id } });
        },
        async confirmDelete(id) {
            this.idToDelete = id;
        },
        async deleteConfirmed() {
            try {
                this.$http.delete(`/groups/${this.idToDelete}`)
                    .then(() => {
                        this.fetchGroups();
                        this.showDeleteModal = false;
                        this.idToDelete = null;
                    });
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
            this.fetchGroups(this.currentPage);
        },
        searchTerm() {
            this.fetchGroups();
        },
        idToDelete(newValue) {
            if (newValue !== null) {
                this.showDeleteModal = true;
            }
        },
    },
    async mounted() {
        await this.fetchGroups();
    },
};
</script>