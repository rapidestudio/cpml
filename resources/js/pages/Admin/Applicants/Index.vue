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
import { Input } from '@/components/ui/input';
import { Eye, Trash2, Download } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    applicants: {
        data: Array<{
            id: number;
            full_name: string;
            email: string;
            phone_number: string;
            created_at: string;
            status: string;
            position?: {
                id: number;
                name: string;
            };
        }>;
        links: Array<any>;
        from: number;
        to: number;
        total: number;
    };
    positions: Array<{ id: number; name: string }>;
    filters?: {
        search?: string;
        status?: string;
        position_id?: string;
        date_range?: string;
        per_page?: number;
    };
}>();

const search = ref(props.filters?.search || '');



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

// Status Helpers
const statusLabels: Record<string, string> = {
    new: 'Baru',
    screening: 'Seleksi Berkas',
    interview: 'Wawancara',
    accepted: 'Diterima',
    rejected: 'Ditolak',
};

const statusColors: Record<string, string> = {
    new: 'bg-blue-100 text-blue-800',
    screening: 'bg-yellow-100 text-yellow-800',
    interview: 'bg-purple-100 text-purple-800',
    accepted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
};

// Filter Refs
const filters = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    position_id: props.filters?.position_id || '',
    date_range: props.filters?.date_range || '',
    per_page: props.filters?.per_page || 10,
});

// Watch Filters
import { debounce } from 'lodash'; // Assuming lodash is available or just use simple timeout

let timeout: any = null;

watch(filters, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/applicants', val, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    }, 500);
}, { deep: true });

// WhatsApp Helper
const getWhatsAppLink = (phone: string, name: string) => {
    // Basic formatting: remove non-numeric, replace 0 with 62
    let number = phone.replace(/\D/g, '');
    if (number.startsWith('0')) {
        number = '62' + number.slice(1);
    }
    const text = `Halo ${name}, kami dari tim rekrutmen...`;
    return `https://wa.me/${number}?text=${encodeURIComponent(text)}`;
};

const selectedIds = ref<number[]>([]);

const toggleSelectAll = (event: Event) => {
    const isChecked = (event.target as HTMLInputElement).checked;
    if (isChecked) {
        selectedIds.value = props.applicants.data.map(a => a.id);
    } else {
        selectedIds.value = [];
    }
};

const isBulkDeleteDialogOpen = ref(false);
const bulkStatus = ref('');

const executeBulkDelete = () => {
    router.delete('/admin/applicants/bulk-destroy', {
        data: { ids: selectedIds.value },
        onFinish: () => {
            isBulkDeleteDialogOpen.value = false;
            selectedIds.value = [];
        }
    });
};

const executeBulkUpdateStatus = () => {
    if (!bulkStatus.value) return;
    
    router.patch('/admin/applicants/bulk-status', {
        ids: selectedIds.value,
        status: bulkStatus.value
    }, {
        onFinish: () => {
             selectedIds.value = [];
             bulkStatus.value = '';
        }
    });
}
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
                        <div class="flex flex-col md:flex-row gap-4 mb-6">
                            <div class="relative w-full md:w-64">
                                <Input v-model="filters.search" placeholder="Cari pelamar..." class="pl-8" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                            
                            <select v-model="filters.position_id" class="flex h-9 w-full md:w-48 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                <option value="">Semua Posisi</option>
                                <option v-for="pos in positions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                            </select>

                             <select v-model="filters.status" class="flex h-9 w-full md:w-48 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                <option value="">Semua Status</option>
                                <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                            </select>

                             <select v-model="filters.date_range" class="flex h-9 w-full md:w-48 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-800 dark:text-white">
                                <option value="">Semua Waktu</option>
                                <option value="today">Hari Ini</option>
                                <option value="week">Minggu Ini</option>
                                <option value="month">Bulan Ini</option>
                            </select>

                             <a :href="`/admin/applicants/export?search=${filters.search}`" target="_blank" class="ml-auto">
                                <Button variant="outline">
                                    <Download class="mr-2 h-4 w-4" /> Export Excel
                                </Button>
                             </a>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="selectedIds.length > 0" class="mb-4 bg-gray-100 p-3 rounded-md flex flex-col md:flex-row justify-between items-center gap-4">
                            <span class="text-sm font-medium">{{ selectedIds.length }} data terpilih</span>
                            
                            <div class="flex items-center gap-2">
                                <select v-model="bulkStatus" class="h-9 rounded-md border border-input bg-white px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring dark:bg-gray-800 dark:text-white">
                                    <option value="">Pilih Status Baru...</option>
                                    <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                                </select>
                                <Button size="sm" @click="executeBulkUpdateStatus" :disabled="!bulkStatus">
                                    Update Status
                                </Button>
                                <div class="w-px h-6 bg-gray-300 mx-2"></div>
                                <Button variant="destructive" size="sm" @click="isBulkDeleteDialogOpen = true">
                                    <Trash2 class="w-4 h-4 mr-2" /> Hapus
                                </Button>
                            </div>
                        </div>

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">
                                        <input type="checkbox" 
                                            class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                                            :checked="selectedIds.length > 0 && selectedIds.length === applicants.data.length"
                                            :indeterminate="selectedIds.length > 0 && selectedIds.length < applicants.data.length"
                                            @change="toggleSelectAll"
                                        />
                                    </TableHead>
                                    <TableHead>Nama Lengkap</TableHead>
                                    <TableHead>Posisi</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>No HP</TableHead>
                                    <TableHead>Tanggal Daftar</TableHead>
                                    <TableHead class="text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="applicants.data.length === 0">
                                    <TableCell colspan="7" class="text-center h-24">
                                        Belum ada data pelamar.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="applicant in applicants.data" :key="applicant.id">
                                    <TableCell>
                                        <input type="checkbox" 
                                            class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary"
                                            :value="applicant.id"
                                            v-model="selectedIds"
                                        />
                                    </TableCell>
                                    <TableCell class="font-medium">
                                        {{ applicant.full_name }}
                                        <div class="text-xs text-gray-500">{{ applicant.email }}</div>
                                    </TableCell>
                                    <TableCell>{{ applicant.position?.name || '-' }}</TableCell>
                                    <TableCell>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="statusColors[applicant.status] || 'bg-gray-100 text-gray-800'">
                                            {{ statusLabels[applicant.status] || applicant.status }}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <a :href="getWhatsAppLink(applicant.phone_number, applicant.full_name)" target="_blank" class="text-green-600 hover:underline flex items-center">
                                            {{ applicant.phone_number }}
                                        </a>
                                    </TableCell>
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
                        
                        <div class="mt-4 flex flex-col md:flex-row justify-between items-center gap-4" v-if="applicants.data.length > 0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">Show</span>
                                <select v-model="filters.per_page" class="h-8 w-16 rounded-md border border-input bg-transparent px-2 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring dark:bg-gray-800 dark:text-white">
                                    <option :value="10">10</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                </select>
                                <span class="text-sm text-gray-500">
                                    entries ({{ applicants.from }} - {{ applicants.to }} of {{ applicants.total }})
                                </span>
                            </div>
                            
                            <div class="flex flex-wrap gap-1">
                                <template v-for="(link, key) in applicants.links" :key="key">
                                    <div v-if="link.url === null" class="mr-1 mb-1 px-3 py-2 text-sm text-gray-400 border rounded" v-html="link.label" />
                                    <Link v-else :href="link.url" class="mr-1 mb-1" preserve-scroll>
                                        <Button 
                                            :variant="link.active ? 'default' : 'outline'" 
                                            size="sm"
                                            class="text-xs"
                                            v-html="link.label"
                                        />
                                    </Link>
                                </template>
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

        <AlertDialog :open="isBulkDeleteDialogOpen" @update:open="isBulkDeleteDialogOpen = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Hapus {{ selectedIds.length }} data terpilih?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Aksi ini akan menghapus semua data pelamar yang dipilih. Data yang dihapus dapat dipulihkan melalui database (Soft Delete).
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction @click="executeBulkDelete" class="bg-red-600 hover:bg-red-700 focus:ring-red-600">
                        Hapus Semua
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
