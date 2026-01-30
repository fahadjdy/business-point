import Swal from 'sweetalert2';

/**
 * Reusable SweetAlert2 Utility
 */
const swal = {
    /**
     * Show a simple toast notification
     */
    toast: (title, icon = 'success') => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: icon,
            title: title
        });
    },

    /**
     * Show a confirmation dialog
     */
    confirm: async (title, text, icon = 'warning') => {
        const result = await Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        });

        return result.isConfirmed;
    },

    /**
     * Show a success message modal
     */
    success: (title, text = '') => {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#3699ff'
        });
    },

    /**
     * Show an error message modal
     */
    error: (title, text = 'Something went wrong!') => {
        return Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#f1416c'
        });
    },

    /**
     * Show a loading alert
     */
    loading: (title = 'Processing...') => {
        Swal.fire({
            title: title,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    /**
     * Close any open Swal
     */
    close: () => {
        Swal.close();
    }
};

export default swal;
