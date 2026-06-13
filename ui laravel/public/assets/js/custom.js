/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

// // ============================================================
// // GLOBAL ERROR HANDLER SYSTEM
// // ============================================================

// const ErrorHandler = {
//     // Error type mapping
//     ERROR_TYPES: {
//         NETWORK: "network",
//         TIMEOUT: "timeout",
//         SERVER: "server",
//         DATABASE: "database",
//         VALIDATION: "validation",
//         UNKNOWN: "unknown",
//     },

//     // Initialize error handling
//     init() {
//         this.setupGlobalErrorHandler();
//         this.setupNetworkListener();
//         this.setupAjaxErrorHandler();
//         this.setupUnhandledRejectionHandler();
//     },

//     // Global error handler for JavaScript errors
//     setupGlobalErrorHandler() {
//         window.addEventListener("error", (event) => {
//             console.error("[ERROR HANDLER] Caught global error:", event.error);
//             this.handleError({
//                 type: this.ERROR_TYPES.UNKNOWN,
//                 message: event.message,
//                 filename: event.filename,
//                 lineno: event.lineno,
//                 error: event.error,
//             });
//         });
//     },

//     // Network connectivity listener
//     setupNetworkListener() {
//         // Check initial network status
//         if (!navigator.onLine) {
//             this.showNetworkError();
//         }

//         // Listen for online/offline events
//         window.addEventListener("online", () => {
//             console.log("[NETWORK] Connection restored");
//             this.hideNetworkError();
//             this.showNotification("Koneksi dipulihkan", "success");
//         });

//         window.addEventListener("offline", () => {
//             console.log("[NETWORK] Connection lost");
//             this.showNetworkError();
//         });
//     },

//     // AJAX/Fetch error handler
//     setupAjaxErrorHandler() {
//         $(document).ajaxError((event, xhr, settings, thrownError) => {
//             console.error("[AJAX ERROR]", {
//                 url: settings.url,
//                 status: xhr.status,
//                 statusText: xhr.statusText,
//                 error: thrownError,
//             });

//             let errorType = this.ERROR_TYPES.UNKNOWN;
//             let errorMessage = "Terjadi kesalahan saat memproses permintaan";

//             // Determine error type based on status code
//             if (xhr.status === 0) {
//                 errorType = this.ERROR_TYPES.NETWORK;
//                 errorMessage =
//                     "Koneksi jaringan terputus. Silakan periksa koneksi internet Anda.";
//             } else if (xhr.status === 408 || xhr.status === 504) {
//                 errorType = this.ERROR_TYPES.TIMEOUT;
//                 errorMessage =
//                     "Permintaan timeout. Server tidak merespons dalam waktu yang ditentukan.";
//             } else if (xhr.status >= 500) {
//                 errorType = this.ERROR_TYPES.SERVER;
//                 errorMessage = this.getServerErrorMessage(xhr.status);
//             } else if (xhr.status === 422) {
//                 errorType = this.ERROR_TYPES.VALIDATION;
//                 errorMessage = "Data tidak valid. Silakan periksa input Anda.";
//             } else if (xhr.status >= 400) {
//                 errorMessage = `Kesalahan klien: ${xhr.statusText}`;
//             }

//             this.handleError({
//                 type: errorType,
//                 message: errorMessage,
//                 status: xhr.status,
//                 response: xhr.responseJSON,
//             });
//         });
//     },

//     // Unhandled promise rejection handler
//     setupUnhandledRejectionHandler() {
//         window.addEventListener("unhandledrejection", (event) => {
//             console.error("[UNHANDLED REJECTION]", event.reason);

//             let errorMessage = "Terjadi kesalahan yang tidak terduga";
//             if (event.reason instanceof Error) {
//                 errorMessage = event.reason.message;
//             } else if (typeof event.reason === "string") {
//                 errorMessage = event.reason;
//             }

//             this.handleError({
//                 type: this.ERROR_TYPES.UNKNOWN,
//                 message: errorMessage,
//                 error: event.reason,
//             });

//             // Prevent the default handling (which would log to console)
//             event.preventDefault();
//         });
//     },

//     // Main error handler
//     handleError(errorObj) {
//         console.error("[ERROR HANDLER] Processing error:", errorObj);

//         // Log to server
//         this.logErrorToServer(errorObj);

//         // Show appropriate UI notification
//         switch (errorObj.type) {
//             case this.ERROR_TYPES.NETWORK:
//                 this.showNetworkError();
//                 break;
//             case this.ERROR_TYPES.TIMEOUT:
//                 this.showTimeoutError();
//                 break;
//             case this.ERROR_TYPES.SERVER:
//                 this.showServerError(errorObj);
//                 break;
//             case this.ERROR_TYPES.DATABASE:
//                 this.showDatabaseError();
//                 break;
//             case this.ERROR_TYPES.VALIDATION:
//                 this.showValidationError(errorObj);
//                 break;
//             default:
//                 this.showGenericError(errorObj.message);
//         }
//     },

//     // Error display functions
//     showNetworkError() {
//         const alertHtml = `
//             <div class="alert alert-danger alert-dismissible fade show network-error-alert" role="alert" style="border-radius: 0;">
//                 <div style="display: flex; align-items: center; gap: 10px;">
//                     <i class="fas fa-wifi-slash" style="font-size: 20px;"></i>
//                     <div>
//                         <strong>Koneksi Jaringan Terputus</strong>
//                         <p style="margin: 5px 0 0 0; font-size: 14px;">
//                             Periksa koneksi internet Anda. Beberapa fitur mungkin tidak tersedia.
//                         </p>
//                     </div>
//                 </div>
//                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
//                     <span aria-hidden="true">&times;</span>
//                 </button>
//             </div>
//         `;

//         // Remove existing network error if any
//         $(".network-error-alert").remove();

//         // Add new alert
//         $(".main-wrapper").prepend(alertHtml);
//     },

//     hideNetworkError() {
//         $(".network-error-alert").fadeOut(() => {
//             $(".network-error-alert").remove();
//         });
//     },

//     showServerError(errorObj) {
//         Swal.fire({
//             icon: "error",
//             title: "Server Error",
//             html: `
//                 <p>Terjadi kesalahan pada server (${errorObj.status || "Unknown"})</p>
//                 <p style="color: #666; font-size: 14px;">
//                     ${errorObj.message || "Silakan coba lagi nanti atau hubungi administrator."}
//                 </p>
//             `,
//             confirmButtonText: "Coba Lagi",
//             confirmButtonColor: "#667eea",
//             allowOutsideClick: false,
//             allowEscapeKey: false,
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 location.reload();
//             }
//         });
//     },

//     showTimeoutError() {
//         Swal.fire({
//             icon: "warning",
//             title: "Timeout",
//             html: `
//                 <p>Permintaan memakan waktu terlalu lama.</p>
//                 <p style="color: #666; font-size: 14px;">
//                     Server tidak merespons. Silakan periksa koneksi internet atau coba lagi.
//                 </p>
//             `,
//             confirmButtonText: "Coba Lagi",
//             confirmButtonColor: "#667eea",
//         });
//     },

//     showDatabaseError() {
//         Swal.fire({
//             icon: "error",
//             title: "Database Error",
//             html: `
//                 <p>Terjadi kesalahan basis data.</p>
//                 <p style="color: #666; font-size: 14px;">
//                     Silakan coba lagi nanti atau hubungi administrator.
//                 </p>
//             `,
//             confirmButtonText: "OK",
//             confirmButtonColor: "#667eea",
//         });
//     },

//     showValidationError(errorObj) {
//         let errorHtml = '<div style="text-align: left;">';

//         if (errorObj.response && errorObj.response.errors) {
//             errorHtml +=
//                 '<strong>Validasi Gagal:</strong><ul style="margin-top: 10px;">';
//             Object.keys(errorObj.response.errors).forEach((field) => {
//                 const fieldErrors = errorObj.response.errors[field];
//                 if (Array.isArray(fieldErrors)) {
//                     fieldErrors.forEach((err) => {
//                         errorHtml += `<li>${err}</li>`;
//                     });
//                 } else {
//                     errorHtml += `<li>${fieldErrors}</li>`;
//                 }
//             });
//             errorHtml += "</ul>";
//         } else {
//             errorHtml += "<p>" + errorObj.message + "</p>";
//         }

//         errorHtml += "</div>";

//         Swal.fire({
//             icon: "warning",
//             title: "Validasi Error",
//             html: errorHtml,
//             confirmButtonText: "OK",
//             confirmButtonColor: "#667eea",
//         });
//     },

//     showGenericError(message) {
//         Swal.fire({
//             icon: "error",
//             title: "Error",
//             text: message || "Terjadi kesalahan yang tidak terduga.",
//             confirmButtonText: "OK",
//             confirmButtonColor: "#667eea",
//         });
//     },

//     // Helper function to get server error message
//     getServerErrorMessage(status) {
//         const messages = {
//             500: "Server mengalami kesalahan internal. Coba lagi nanti.",
//             501: "Fitur ini belum diimplementasikan.",
//             502: "Gateway error. Koneksi ke server gagal.",
//             503: "Server sedang dalam pemeliharaan. Coba lagi nanti.",
//             504: "Server timeout. Coba lagi nanti.",
//         };
//         return messages[status] || "Server error. Silakan coba lagi.";
//     },

//     // Notification helper
//     showNotification(message, type = "info") {
//         const alertClass =
//             {
//                 success: "alert-success",
//                 error: "alert-danger",
//                 warning: "alert-warning",
//                 info: "alert-info",
//             }[type] || "alert-info";

//         const alertHtml = `
//             <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
//                 ${message}
//                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
//                     <span aria-hidden="true">&times;</span>
//                 </button>
//             </div>
//         `;

//         $(".section").prepend(alertHtml);

//         // Auto dismiss after 5 seconds
//         setTimeout(() => {
//             $(".alert").fadeOut(() => $(".alert").remove());
//         }, 5000);
//     },

//     // Log error to server
//     logErrorToServer(errorObj) {
//         // Send error log to server via AJAX (non-blocking)
//         navigator.sendBeacon(
//             "/api/error-log",
//             JSON.stringify({
//                 type: errorObj.type,
//                 message: errorObj.message,
//                 url: window.location.href,
//                 timestamp: new Date().toISOString(),
//                 userAgent: navigator.userAgent,
//                 stack: errorObj.error?.stack || null,
//             }),
//         );
//     },
// };

// // Initialize error handler when document is ready
// $(document).ready(function () {
//     ErrorHandler.init();
// });
