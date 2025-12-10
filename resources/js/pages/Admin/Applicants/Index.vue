<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Eye, Trash2 } from 'lucide-vue-next'

import { Input } from '@/components/ui/input';
import { Download } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    applicants: {
        data: Array<{
            id: number;
            full_name: string;
            email: string;
            phone_number: string;
            created_at: string;
        }>;
        links: Array<any>;
    };
    filters?: {
        search?: string;
    };
}>();

const search = ref(props.filters?.search || '');

watch(search, (value) => {
    router.get(
        '/admin/applicants',
        { search: value },
        { 
            preserveState: true, 
            replace: true, 
            preserveScroll: true 
        }
    );
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};

import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'

const isDeleteDialogOpen = ref(false);
const applicantToDelete = ref<number | null>(null);

const confirmDelete = (id: number) => {
    applicantToDelete.value = id;
    isDeleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (applicantToDelete.value) {
        router.delete(`/admin/applicants/${applicantToDelete.value}`, {
            onFinish: () => {
                isDeleteDialogOpen.value = false;
                applicantToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <Head title="Data Pelamar" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Data Pelamar
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Daftar Pelamar</CardTitle>
                        <div class="flex items-center space-x-2">
                             <div class="relative w-64">
                                <Input v-model="search" placeholder="Cari pelamar..." class="pl-8" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                             </div>
                             <a :href="`/admin/applicants/export?search=${search}`" target="_blank">
                                <Button variant="outline">
                                    <Download class="mr-2 h-4 w-4" /> Export Excel
                                </Button>
                             </a>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama Lengkap</TableHead>
                                    <TableHead>Posisi</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>No HP</TableHead>
                                    <TableHead>Tanggal Daftar</TableHead>
                                    <TableHead class="text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="applicants.data.length === 0">
                                    <TableCell colspan="5" class="text-center h-24">
                                        Belum ada data pelamar.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="applicant in applicants.data" :key="applicant.id">
                                    <TableCell class="font-medium">{{ applicant.full_name }}</TableCell>
                                    <TableCell class="font-medium">{{ applicant.position?.name || '-' }}</TableCell>
                                    <TableCell>{{ applicant.email }}</TableCell>
                                    <TableCell>{{ applicant.phone_number }}</TableCell>
                                    <TableCell>{{ formatDate(applicant.created_at) }}</TableCell>


                                    <TableCell class="text-right">
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="`/admin/applicants/${applicant.id}`">
                                                <Button variant="outline" size="icon" title="Lihat Detail">
                                                    <Eye class="w-4 h-4" />
                                                </Button>
                                            </Link>
                                            <Button variant="destructive" size="icon" @click="confirmDelete(applicant.id)" title="Hapus">
                                                <Trash2 class="w-4 h-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        
                        <!-- Pagination Logic (Simplified) -->
                        <div class="mt-4 flex justify-between items-center" v-if="applicants.data.length > 0">
                            <!-- Use Inertia Links for pagination here if needed, keeping it simple for now -->
                             <div class="text-sm text-gray-500">
                                Menampilkan {{ applicants.data.length }} data.
                            </div>
                        </div>

                    </CardContent>
                </Card>
            </div>
        </div>

        
        <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Apakah Anda yakin?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Aksi ini akan menghapus data pelamar. Data yang dihapus masih dapat dipulihkan melalui database jika diperlukan (Soft Delete).
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction @click="executeDelete" class="bg-red-600 hover:bg-red-700 focus:ring-red-600">
                        Hapus
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
