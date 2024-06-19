<template>
    <div class="group-form">
        <h2>{{ isEdit ? 'Edit Group' : 'Create Group' }}</h2>
        <b-form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-6">
                    <h3>Group name</h3>
                    <b-form-group label-for="name">
                        <b-form-input id="name" v-model="group.name" required />
                    </b-form-group>

                    <hr>

                    <h3>Direct contacts</h3>
                    <b-form-group label-for="direct-contacts">
                        <b-form-select v-model="selectedDirectContact" :options="availableContacts" value-field="id" text-field="full_name" @change="addDirectContact" />
                        <ul>
                            <li v-for="(contact, index) in directContacts" :key="index">
                                {{ getContactName(contact.id) }}
                                <bF-button variant="danger" size="sm" @click="removeDirectContact(index)">Remove</bF-button>
                            </li>
                        </ul>
                    </b-form-group>

                    <hr>

                    <h3>Children Groups</h3>
                    <b-form-group label-for="child-groups">
                        <b-form-select v-model="selectedChildGroup" :options="availableGroups" value-field="id" text-field="name" @change="addChildGroup" />
                        <ul>
                            <li v-for="(childGroup, index) in group.child_group_ids" :key="index">
                                {{ getGroupName(childGroup) }}
                                <b-button variant="danger" size="sm" @click="removeChildGroup(index)">Remove</b-button>
                            </li>
                        </ul>
                    </b-form-group>
                </div>
                <div class="col-md-6">
                    <h3>Parent Groups</h3>
                    <b-form-group label-for="parent-groups">
                        <b-form-select v-model="selectedParentGroup" :options="availableGroups" value-field="id" text-field="name" @change="addParentGroup" />
                        <ul>
                            <li v-for="(parentGroup, index) in group.parent_group_ids" :key="index">
                                {{ getGroupName(parentGroup) }}
                                <b-button variant="danger" size="sm" @click="removeParentGroup(index)">Remove</b-button>
                            </li>
                        </ul>
                    </b-form-group>

                    <hr>

                    <h3>Inherited Contacts</h3>
                    <ul>
                        <li v-for="contact in inheritedContacts" :key="contact.id">
                            {{ contact.first_name }} {{ contact.last_name }}
                        </li>
                    </ul>
                </div>
            </div>
            <b-button type="submit" variant="primary" class="mx-2" :disabled="!isDirty">{{ isEdit ? 'Update' : 'Create' }}</b-button>
            <b-button type="button" variant="secondary" @click="navigateBack">Cancel</b-button>
        </b-form>
    </div>
</template>

<script>
import { BButton, BForm, BFormGroup, BFormInput, BFormSelect } from 'bootstrap-vue-next';

export default {
    name: 'GroupForm',
    components: {
        BForm,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BButton,
    },
    props: {
        id: {
            type: [Number, String],
            default: null,
        },
    },
    data() {
        return {
            group: {
                name: '',
                parent_group_ids: [],
                child_group_ids: [],
                direct_contact_ids: [],
            },
            initialGroup: {},
            contacts: [],
            groups: [],
            isEdit: false,
            selectedChildGroup: null,
            selectedParentGroup: null,
            selectedDirectContact: null,
        };
    },
    computed: {
        availableGroups() {
            return this.groups.filter(group =>
                (
                    (this.group.id && group.id.toString() !== this.group.id.toString())
                    || !this.isEdit
                )
                && !this.group.parent_group_ids.includes(group.id.toString())
                && !this.group.child_group_ids.includes(group.id.toString())
            );
        },
        availableContacts() {
            return this.contacts.filter(contact =>
                (
                    !(this.group.direct_contact_ids.includes(contact.id.toString()))
                    && !this.inheritedContacts.find(inheritedContact => inheritedContact.id.toString() === contact.id.toString())
                )
            ).map(contact => ({
                ...contact,
                full_name: `${contact.first_name} ${contact.last_name}`
            }))
        },
        inheritedContacts() {
            const parentGroups = this.groups.filter(group => this.group.parent_group_ids.includes(group.id.toString()));
            const inheritedContacts = [];
            parentGroups.forEach(group => {
                group.direct_contact_ids.forEach(contactId => {
                    if (!inheritedContacts.includes(contactId)) {
                        inheritedContacts.push(contactId);
                    }
                });
                group.inherited_contact_ids.forEach(contactId => {
                    if (!inheritedContacts.includes(contactId)) {
                        inheritedContacts.push(contactId);
                    }
                });
            });
            return this.contacts.filter(contact => inheritedContacts.includes(contact.id.toString()));
        },
        directContacts() {
            return this.contacts.filter(contact => (
                this.group.direct_contact_ids.includes(contact.id.toString())
                && !this.inheritedContacts.find(inheritedContact => inheritedContact.id.toString() === contact.id.toString())
            ));
        },
        isDirty() {
            return JSON.stringify(this.group) !== JSON.stringify(this.initialGroup) && this.group.name.trim() !== '';
        },
    },
    methods: {
        async fetchContacts() {
            await this.$http.get('/addresses?per_page=999999')
                .then(response => {
                    this.contacts = response.data.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        async fetchGroups() {
            await this.$http.get('/groups?per_page=999999')
                .then(response => {
                    this.groups = response.data.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        async fetchGroupData() {
            await this.$http.get(`/groups/${this.id}`)
                .then(response => {
                    this.group = response.data;
                    this.initialGroup = JSON.parse(JSON.stringify(response.data));
                })
                .catch(error => {
                    console.error(error);
                });
        },
        async submitForm() {
            if (this.isEdit) {
                await this.$http.put(`/groups/${this.id}`, this.group);
            } else {
                await this.$http.post('/groups', this.group);
            }
            this.navigateBack();
        },
        addParentGroup() {
            if (this.selectedParentGroup) {
                this.group.parent_group_ids.push(this.selectedParentGroup.toString());
                this.selectedParentGroup = null;
            }
        },
        removeParentGroup(index) {
            this.group.parent_group_ids.splice(index, 1);
        },
        addChildGroup() {
            if (this.selectedChildGroup) {
                this.group.child_group_ids.push(this.selectedChildGroup.toString());
                this.selectedChildGroup = null;
            }
        },
        removeChildGroup(index) {
            this.group.child_group_ids.splice(index, 1);
        },
        addDirectContact() {
            if (this.selectedDirectContact) {
                this.group.direct_contact_ids.push(this.selectedDirectContact.toString());
                this.selectedDirectContact = null;
            }
        },
        removeDirectContact(index) {
            this.group.direct_contact_ids.splice(index, 1);
        },
        getGroupName(groupId) {
            const group = this.groups.find(group => group.id.toString() === groupId.toString());
            return group ? group.name : '';
        },
        getContactName(contactId) {
            const contact = this.contacts.find(contact => contact.id.toString() === contactId.toString());
            return contact ? `${contact.first_name} ${contact.last_name}` : '';
        },
        navigateBack() {
            this.$router.push({name: 'group-list'});
        },
    },
    async mounted() {
        await this.fetchContacts();
        await this.fetchGroups();
        if (this.id) {
            this.isEdit = true;
            await this.fetchGroupData();
        }
    },
};
</script>
<style scoped>
.group-form {
    padding: 20px;
}

li {
    margin-bottom: 4px;
    margin-top: 4px;
}
</style>