<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
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
import { Switch } from '@/components/ui/switch';
import { Pencil, Trash2, Plus } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    positions: Array<{
        id: number;
        name: string;
        is_active: boolean;
        created_at: string;
    }>;
}>();

const isDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const editingPosition = ref<any>(null);
const positionToDelete = ref<number | null>(null);

const form = useForm({
    name: '',
    is_active: true,
});

const openCreateDialog = () => {
    editingPosition.value = null;
    form.reset();
    isDialogOpen.value = true;
};

const openEditDialog = (position: any) => {
    editingPosition.value = position;
    form.name = position.name;
    form.is_active = !!position.is_active;
    isDialogOpen.value = true;
};

const confirmDelete = (id: number) => {
    positionToDelete.value = id;
    isDeleteDialogOpen.value = true;
};

const submitForm = () => {
    if (editingPosition.value) {
        form.put(`/admin/positions/${editingPosition.value.id}`, {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post('/admin/positions', {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            }
        });
    }
};

const executeDelete = () => {
    if (positionToDelete.value) {
        form.delete(`/admin/positions/${positionToDelete.value}`, {
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                positionToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <Head title="Manajemen Posisi" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Manajemen Posisi
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Daftar Posisi</CardTitle>
                        <Button @click="openCreateDialog">
                            <Plus class="mr-2 h-4 w-4" /> Tambah Posisi
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama Posisi</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="positions.length === 0">
                                    <TableCell colspan="3" class="text-center h-24 text-gray-500">
                                        Belum ada data posisi.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="position in positions" :key="position.id">
                                    <TableCell class="font-medium">{{ position.name }}</TableCell>
                                    <TableCell>
                                        <span :class="position.is_active ? 'text-green-600' : 'text-red-600'" class="font-bold text-sm">
                                            {{ position.is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end space-x-2">
                                            <Button variant="outline" size="icon" @click="openEditDialog(position)">
                                                <Pencil class="w-4 h-4" />
                                            </Button>
                                            <Button variant="destructive" size="icon" @click="confirmDelete(position.id)">
                                                <Trash2 class="w-4 h-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingPosition ? 'Edit Posisi' : 'Tambah Posisi' }}</DialogTitle>
                    <DialogDescription>
                        Isi detail posisi di bawah ini.
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label for="name">Nama Posisi</Label>
                        <Input id="name" v-model="form.name" placeholder="Contoh: Manager Marketing" />
                        <span v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                        <Label for="is_active">Aktif</Label>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="isDialogOpen = false">Batal</Button>
                    <Button @click="submitForm" :disabled="form.processing">Simpan</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation -->
        <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Hapus Posisi?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Posisi yang dihapus tidak dapat dipilih lagi oleh pelamar baru.
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
