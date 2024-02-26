<script>
import { ref, watch } from 'vue';
import Dropdown from './Dropdown.vue';
import Pagination from './Pagination.vue';
import Modal from './Modal.vue';

export default {
    props: {
        data: Object,
        schools: {
            type: Object,
            default: {}
        }
    },
    components: {
        Pagination,
        Dropdown,
        Modal,
    },
    methods: {
        formatDate(date) {
            const options = { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        },
    },
    setup(props) {
        const filteredGroups = ref([]);
        const isUpdating = ref(false);
        const selectedSchool = ref(null);
        const showModal = ref(false);
        const showEditModal = ref(false);
        const showViewModal = ref(false);
        const maxWidth = ref('md');
        const closeable = ref(true);
        const showAddStudentForm = ref(false);
        const viewInfo = ref({
            viewInfo: {},
        });
        const editedStudent = ref({
            id: null,
            name: '',
            email: '',
            school: null,
            group: null,
        });
        const newStudent = ref({
            name: '',
            email: '',
            school: null,
            group: null,
        });

        const openEditModal = (student) => {
            showEditModal.value = true;
            editedStudent.value.id = student.id;
            editedStudent.value.name = student.name;
            editedStudent.value.email = student.email;
            editedStudent.value.school = student.group.school.id;
            editedStudent.value.group = student.group.id;
            selectedSchool.value = student.group.school.id;
        };

        const openViewModal = (student) => {
            console.log(student);
            showViewModal.value = true;
            viewInfo.value = student
        };

        const closeViewModal = () => {
            showViewModal.value = false;
        };

        const closeEditModal = () => {
            showEditModal.value = false;
        };

        const handleEditSubmit = async () => {
            isUpdating.value = true;
            try {
                const response = await axios.put(`/api/students/${editedStudent.value.id}`, {
                    name: editedStudent.value.name,
                    email: editedStudent.value.email,
                    group_id: editedStudent.value.group,
                });

                closeEditModal();
                alert(response.data.message)
                window.location.reload();
            } catch (error) {
                closeEditModal();
                alert('Falha durante atualização');
                window.location.reload();
            } finally {
                isUpdating.value = false;
            }
        };

        const handleSchoolChange = (event) => {
            selectedSchool.value = Number(event.target.value);
        };

        watch([selectedSchool, () => props.schools], ([school, schools]) => {
            const selectedSchoolData = schools.find(s => s.id === school);
            filteredGroups.value = selectedSchoolData ? selectedSchoolData.groups : [];
        });

        const deleteStudent = async (studentId) => {

            isUpdating.value = true;
            if (!confirm("Confirma a exclusão desse estudante?")) {
                return;
            }
            try {
                const response = await axios.delete(`/api/students/${studentId}`);
                alert(response.data.message)
                window.location.reload();
            } catch (error) {
                alert('Falha durante exclusão');
            } finally {
                isUpdating.value = false;
            }
        };

        const openCreateModal = () => {
            showModal.value = true;
        };

        const closeCreateModal = () => {
            showModal.value = false;
        };

        const openAddStudentForm = () => {
            showModal.value = true;
            selectedSchool.value = null;
        };

        const handleAddSchoolChange = (event) => {
            selectedSchool.value = Number(event.target.value);
        };

        const handleAddSubmit = async () => {
            isUpdating.value = true;
            try {
                const response = await axios.post('/api/students/create', {
                    name: newStudent.value.name,
                    email: newStudent.value.email,
                    group_id: newStudent.value.group,
                });

                closeCreateModal();
                alert(response.data.message);
                window.location.reload();
            } catch (error) {
                closeCreateModal();
                alert('Falha durante a adição do estudante: ' + error.message);
                // window.location.reload();
            } finally {
                isUpdating.value = false;
            }
        };
        return {
            showModal,
            showEditModal,
            maxWidth,
            closeable,
            isUpdating,
            editedStudent,
            selectedSchool,
            filteredGroups,
            handleSchoolChange,
            openEditModal,
            closeEditModal,
            handleEditSubmit,
            deleteStudent,
            newStudent,
            closeCreateModal,
            openCreateModal,
            handleAddSubmit,
            handleAddSchoolChange,
            openAddStudentForm,
            openViewModal,
            closeViewModal,
            viewInfo,
            showViewModal

        };
    },
};
</script>

<template>
    <div class="not-prose relative rounded-xl overflow-hidden ">
        <div class="flex justify-end py-2 px-4 mt-4">
            <button v-if="$page.props.auth.user" @click="() => openAddStudentForm()"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14m-7 7V5" />
                </svg> Adicionar Estudante
            </button>
        </div>
        <div v-if="data.data.length > 0" class="relative rounded-xl overflow-auto ">
            <div class="shadow-sm overflow-hidden py-4">
                <div class="relative overflow-x-auto px-1">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 my-8">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Aluno
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Turma
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Escola
                                </th>
                                <th v-if="$page.props.auth.user" scope="col" class="px-6 py-3">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="student in data.data" :key="student.id">
                                <tr class="bg-white border-b">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ student.name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ student.email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ student.group.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ student.group.school.name }}
                                    </td>
                                    <td v-if="$page.props.auth.user" class="px-6 py-4 flex items-center ">

                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <button @click="() => openEditModal(student)" type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                                <svg class="w-3 h-3 me-2" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z" />
                                                </svg>

                                                Editar
                                            </button>
                                            <button @click="() => openViewModal(student)" type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                                <svg class="w-3 h-3 me-2" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M10 11h2v5m-2 0h4m-2.6-8.5h0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                                Histórico
                                            </button>
                                            <button @click="() => deleteStudent(student.id)" type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-red-100 hover:text-red-700 focus:z-10 focus:ring-2 focus:ring-red-700 focus:text-red-700">
                                                <svg class="w-3 h-3 me-2" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M20 10H4v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8ZM9 13v-1h6v1c0 .6-.4 1-1 1h-4a1 1 0 0 1-1-1Z"
                                                        clip-rule="evenodd" />
                                                    <path d="M2 6c0-1.1.9-2 2-2h16a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Z" />
                                                </svg>

                                                Deletar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <Pagination :links="data" />
            <div id="editModal">
                <Modal :show="showEditModal" :maxWidth="maxWidth" :closeable="closeable" @close="closeEditModal">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-white rounded-lg shadow">
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                                <div class="">
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 ">
                                    Atualize o registro do aluno
                                </h3>
                                <button @click="closeEditModal" type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                    data-modal-toggle="editModal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <form @submit.prevent="handleEditSubmit" class="p-4 md:p-5"
                                :class="{ 'jet-loading': isUpdating }">
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                    <div class="col-span-2">
                                        <label for="editName"
                                            class="block mb-2 text-sm font-medium text-gray-900 ">Nome</label>
                                        <input v-model="editedStudent.name" id="editName" type="text" name="name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                            required="">
                                    </div>
                                    <div class="col-span-2">
                                        <label for="editEmail"
                                            class="block mb-2 text-sm font-medium text-gray-900 ">E-mail</label>
                                        <input v-model="editedStudent.email" id="editEmail" type="email" name="email"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                            required />
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <label for="editSchool"
                                            class="block mb-2 text-sm font-medium text-gray-900 ">Escola</label>
                                        <select v-model="editedStudent.school" id="editSchool" @change="handleSchoolChange"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                            <option selected="" disabled>Select school</option>
                                            <option v-for="school in schools" :key="school.id" :value="school.id">{{
                                                school.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <label for="editGroup"
                                            class="block mb-2 text-sm font-medium text-gray-900 ">Turma</label>
                                        <select v-model="editedStudent.group" id="editGroup"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                            <option selected="" disabled>Select group</option>
                                            <option v-for="group in filteredGroups" :key="group.id" :value="group.id">{{
                                                group.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <span v-if="isUpdating" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"> <svg aria-hidden="true"
                                            class="inline w-8 h-8 text-gray-200 animate-spin fill-gray-600 "
                                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="currentColor" />
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentFill" />
                                        </svg></span>
                                    <span v-else>Salvar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </Modal>
            </div>
            <div id="viewModal">
                <Modal :show="showViewModal" :maxWidth="maxWidth" :closeable="closeable" @close="closeViewModal">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-white rounded-lg shadow">
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                                <h3 class="text-lg font-semibold text-gray-900 ">
                                    Linha do tempo do aluno
                                </h3>
                                <button @click="closeViewModal" type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                    data-modal-toggle="viewModal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <div v-if="viewInfo.logs.length > 0" class="m-5">
                                <ol class="relative border-s border-gray-200 dark:border-gray-700 p-2">
                                    <li v-for="(log, index) in viewInfo.logs.slice().reverse()" :key="index"
                                        class="mb-10 ms-4">
                                        <div
                                            class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                                        </div>
                                        <time
                                            class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                                            {{ formatDate(log.created_at) }}
                                        </time>
                                        <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                                            Saiu da turma <b>{{ log.group_name }}</b> | escola <b>{{ log.school_name }}</b>.
                                        </p>
                                    </li>
                                </ol>
                            </div>
                            <div v-else class="text-center px-4 py-4">
                                <p> Nenhuma modificação.</p>
                            </div>
                        </div>
                    </div>
                </Modal>
            </div>

        </div>
        <div v-else class="text-center px-4 py-4">
            <p> Nenhum registro encontrado.</p>
        </div>
        <div id="createModal">
            <Modal :show="showModal" :maxWidth="maxWidth" :closeable="closeable" @close="closeCreateModal">
                <div class="relative w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                            <h3 class="text-lg font-semibold text-gray-900 ">
                                Adicione um novo registro de aluno
                            </h3>
                            <button @click="closeCreateModal" type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                data-modal-toggle="createModal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                            </button>
                        </div>
                        <form @submit.prevent="handleAddSubmit" class="p-4 md:p-5" :class="{ 'jet-loading': isUpdating }">
                            <div class="grid gap-4 mb-4 grid-cols-2">
                                <div class="col-span-2">
                                    <label for="createName"
                                        class="block mb-2 text-sm font-medium text-gray-900 ">Nome</label>
                                    <input v-model="newStudent.name" id="createName" type="text" name="name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                        required="" placeholder="Nome do aluno" />
                                </div>
                                <div class="col-span-2">
                                    <label for="createEmail"
                                        class="block mb-2 text-sm font-medium text-gray-900 ">E-mail</label>
                                    <input v-model="newStudent.email" id="createEmail" type="email" name="email"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                        required placeholder="E-mail do aluno" />
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label for="createSchool"
                                        class="block mb-2 text-sm font-medium text-gray-900 ">Escola</label>
                                    <select v-model="newStudent.school" id="createSchool" @change="handleAddSchoolChange"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                                        required>
                                        <option selected="" disabled>Select school</option>
                                        <option v-for="school in schools" :key="school.id" :value="school.id">{{
                                            school.name }}</option>
                                    </select>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label for="createGroup"
                                        class="block mb-2 text-sm font-medium text-gray-900 ">Turma</label>
                                    <select v-model="newStudent.group" id="createGroup"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                                        required>
                                        <option selected="" disabled>Select group</option>
                                        <option v-for="group in filteredGroups" :key="group.id" :value="group.id">{{
                                            group.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit"
                                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                <span v-if="isUpdating" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"> <svg aria-hidden="true"
                                        class="inline w-8 h-8 text-gray-200 animate-spin fill-gray-600 "
                                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="currentColor" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentFill" />
                                </svg></span>
                            <span v-else>Salvar</span>
                        </button>
                    </form>
                </div>
            </div>
        </Modal>
    </div>
    <div class="absolute inset-0 pointer-events-none border border-black/5 rounded-xl dark:border-white/5">
    </div>
</div></template>

