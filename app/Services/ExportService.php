<?php

namespace App\Services;

use App\Models\ContactBook;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;

class ExportService
{
    /**
     * Export contacts to CSV format
     */
    public function exportToCsv(Collection $contacts): Response
    {
        $filename = 'community-directory-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add header with brand info
            $brandInfo = $this->getBrandInfo();
            fputcsv($file, [$brandInfo['name']]);
            fputcsv($file, [$brandInfo['tagline']]);
            fputcsv($file, ['Support: ' . $brandInfo['support_phone'] . ' | ' . $brandInfo['support_email']]);
            fputcsv($file, ['Generated on: ' . date('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty row
            
            // CSV headers
            fputcsv($file, [
                'Name',
                'Type',
                'Phone',
                'Email',
                'Designation',
                'Department',
                'Address',
                'Tags',
                'Status',
                'Created Date'
            ]);

            // Data rows
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->name,
                    ucfirst($contact->type),
                    $contact->phone,
                    $contact->email,
                    $contact->designation,
                    $contact->department,
                    $contact->address,
                    $contact->tags->pluck('name')->join(', '),
                    $contact->is_active ? 'Active' : 'Inactive',
                    $contact->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export contacts to Excel-like CSV format
     */
    public function exportToExcel(Collection $contacts): Response
    {
        // For now, we'll create an enhanced CSV that works well with Excel
        $filename = 'community-directory-' . date('Y-m-d-H-i-s') . '.xlsx.csv';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add header with brand info
            $brandInfo = $this->getBrandInfo();
            fputcsv($file, ['COMMUNITY DIRECTORY REPORT'], ';');
            fputcsv($file, [$brandInfo['name']], ';');
            fputcsv($file, [$brandInfo['tagline']], ';');
            fputcsv($file, ['Support Phone: ' . $brandInfo['support_phone']], ';');
            fputcsv($file, ['Support Email: ' . $brandInfo['support_email']], ';');
            fputcsv($file, ['Generated: ' . date('Y-m-d H:i:s')], ';');
            fputcsv($file, ['Total Records: ' . $contacts->count()], ';');
            fputcsv($file, [], ';'); // Empty row
            
            // Excel-style headers with formatting
            fputcsv($file, [
                'Name',
                'Type',
                'Phone',
                'Email',
                'Designation',
                'Department',
                'Address',
                'Tags',
                'Status',
                'Created Date'
            ], ';');

            // Data rows
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->name,
                    ucfirst($contact->type),
                    $contact->phone,
                    $contact->email,
                    $contact->designation,
                    $contact->department,
                    $contact->address,
                    $contact->tags->pluck('name')->join(', '),
                    $contact->is_active ? 'Active' : 'Inactive',
                    $contact->created_at->format('Y-m-d H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export contacts to PDF format
     */
    public function exportToPdf(Collection $contacts): Response
    {
        $filename = 'community-directory-' . date('Y-m-d-H-i-s') . '.pdf';
        $brandInfo = $this->getBrandInfo();
        
        // Generate actual PDF content
        $pdfContent = $this->generatePdfContent($contacts, $brandInfo);
        
        // Return as downloadable PDF file
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        return response($pdfContent, 200, $headers);
    }

    /**
     * Get brand information from settings
     */
    private function getBrandInfo(): array
    {
        return [
            'name' => Setting::getValue('app_name', 'Business Point'),
            'tagline' => Setting::getValue('app_tagline', 'Your Community Directory'),
            'support_phone' => Setting::getValue('support_phone', '+1-234-567-8900'),
            'support_email' => Setting::getValue('support_email', 'support@businesspoint.com'),
            'logo_url' => Setting::getValue('app_logo', ''),
        ];
    }

    /**
     * Generate HTML content for PDF
     */
    private function generatePdfHtml(Collection $contacts, array $brandInfo): string
    {
        $totalPages = ceil($contacts->count() / 20); // 20 records per page
        $currentPage = 1;
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Community Directory</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .brand-name { font-size: 24px; font-weight: bold; margin: 10px 0; }
        .tagline { font-size: 14px; color: #666; margin: 5px 0; }
        .support-info { font-size: 12px; color: #888; margin: 5px 0; }
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); 
                    font-size: 72px; color: rgba(0,0,0,0.1); z-index: -1; pointer-events: none; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { position: fixed; bottom: 20px; right: 20px; font-size: 10px; color: #666; }
        .page-break { page-break-after: always; }
        .tags { font-size: 10px; color: #0066cc; }
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: red; }
    </style>
</head>
<body>';

        // Add watermark
        $html .= '<div class="watermark">' . strtoupper($brandInfo['name']) . '</div>';

        // Header
        $html .= '<div class="header">';
        if ($brandInfo['logo_url']) {
            $html .= '<img src="' . $brandInfo['logo_url'] . '" alt="Logo" class="logo"><br>';
        }
        $html .= '<div class="brand-name">' . $brandInfo['name'] . '</div>';
        $html .= '<div class="tagline">' . $brandInfo['tagline'] . '</div>';
        $html .= '<div class="support-info">Phone: ' . $brandInfo['support_phone'] . ' | Email: ' . $brandInfo['support_email'] . '</div>';
        $html .= '<div class="support-info">Generated on: ' . date('Y-m-d H:i:s') . ' | Total Records: ' . $contacts->count() . '</div>';
        $html .= '</div>';

        // Table
        $html .= '<table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Tags</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

        $recordCount = 0;
        foreach ($contacts as $contact) {
            if ($recordCount > 0 && $recordCount % 20 === 0) {
                $currentPage++;
                $html .= '</tbody></table><div class="page-break"></div>';
                $html .= '<table><thead><tr><th>Name</th><th>Type</th><th>Phone</th><th>Email</th><th>Designation</th><th>Tags</th><th>Status</th></tr></thead><tbody>';
            }

            $statusClass = $contact->is_active ? 'status-active' : 'status-inactive';
            $statusText = $contact->is_active ? 'Active' : 'Inactive';
            
            $html .= '<tr>
                <td><strong>' . htmlspecialchars($contact->name) . '</strong></td>
                <td>' . ucfirst($contact->type) . '</td>
                <td>' . htmlspecialchars($contact->phone) . '</td>
                <td>' . htmlspecialchars($contact->email) . '</td>
                <td>' . htmlspecialchars($contact->designation) . '</td>
                <td class="tags">' . htmlspecialchars($contact->tags->pluck('name')->join(', ')) . '</td>
                <td class="' . $statusClass . '">' . $statusText . '</td>
            </tr>';
            
            $recordCount++;
        }

        $html .= '</tbody></table>';

        // Footer
        $html .= '<div class="footer">Page ' . $currentPage . ' of ' . $totalPages . '</div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate HTML content optimized for PDF printing
     */
    private function generatePrintableHtml(Collection $contacts, array $brandInfo): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Community Directory</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.4;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333; 
            padding-bottom: 20px; 
        }
        .brand-name { 
            font-size: 24px; 
            font-weight: bold; 
            margin: 10px 0; 
            color: #2c3e50;
        }
        .tagline { 
            font-size: 14px; 
            color: #666; 
            margin: 5px 0; 
        }
        .support-info { 
            font-size: 12px; 
            color: #888; 
            margin: 5px 0; 
        }
        .print-instructions {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .contact-item {
            border: 1px solid #ddd;
            margin: 15px 0;
            padding: 15px;
            border-radius: 5px;
            background: #fafafa;
            page-break-inside: avoid;
        }
        .contact-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .contact-type {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .contact-details {
            margin: 8px 0;
        }
        .contact-detail {
            margin: 4px 0;
            font-size: 14px;
        }
        .contact-tags {
            margin-top: 10px;
        }
        .tag {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin: 2px;
        }
        .status-active { color: #27ae60; font-weight: bold; }
        .status-inactive { color: #e74c3c; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @page {
            margin: 1in;
        }
    </style>
    <script>
        window.onload = function() {
            // Show a brief instruction message
            setTimeout(function() {
                if (confirm("Ready to save as PDF?\\n\\nClick OK to open the print dialog, then select \\"Save as PDF\\" as your destination.")) {
                    window.print();
                }
            }, 1000);
        }
        
        // Add keyboard shortcut
        document.addEventListener("keydown", function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === "p") {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</head>
<body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<div class="brand-name">' . htmlspecialchars($brandInfo['name']) . '</div>';
        $html .= '<div class="tagline">' . htmlspecialchars($brandInfo['tagline']) . '</div>';
        $html .= '<div class="support-info">Phone: ' . htmlspecialchars($brandInfo['support_phone']) . ' | Email: ' . htmlspecialchars($brandInfo['support_email']) . '</div>';
        $html .= '<div class="support-info">Generated on: ' . date('Y-m-d H:i:s') . ' | Total Records: ' . $contacts->count() . '</div>';
        $html .= '</div>';

        // Print instructions
        $html .= '<div class="print-instructions no-print">';
        $html .= '<div style="background: #e3f2fd; border: 2px solid #2196f3; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">';
        $html .= '<h3 style="color: #1976d2; margin-top: 0;">📄 How to Save as PDF</h3>';
        $html .= '<div style="font-size: 16px; line-height: 1.6; color: #333;">';
        $html .= '<p><strong>Step 1:</strong> Press <kbd style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px; border: 1px solid #ccc;">Ctrl+P</kbd> (Windows) or <kbd style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px; border: 1px solid #ccc;">Cmd+P</kbd> (Mac)</p>';
        $html .= '<p><strong>Step 2:</strong> In the print dialog, select <strong>"Save as PDF"</strong> as destination</p>';
        $html .= '<p><strong>Step 3:</strong> Click <strong>"Save"</strong> to download your PDF file</p>';
        $html .= '<button onclick="window.print()" style="background: #2196f3; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px;">🖨️ Print / Save as PDF</button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Contacts
        foreach ($contacts as $contact) {
            $html .= '<div class="contact-item">';
            $html .= '<div class="contact-name">' . htmlspecialchars($contact->name) . '</div>';
            $html .= '<div class="contact-type">' . ucfirst($contact->type) . '</div>';
            
            $html .= '<div class="contact-details">';
            $html .= '<div class="contact-detail"><strong>Phone:</strong> ' . htmlspecialchars($contact->phone) . '</div>';
            
            if ($contact->email) {
                $html .= '<div class="contact-detail"><strong>Email:</strong> ' . htmlspecialchars($contact->email) . '</div>';
            }
            
            if ($contact->designation) {
                $html .= '<div class="contact-detail"><strong>Designation:</strong> ' . htmlspecialchars($contact->designation) . '</div>';
            }
            
            if ($contact->department) {
                $html .= '<div class="contact-detail"><strong>Department:</strong> ' . htmlspecialchars($contact->department) . '</div>';
            }
            
            if ($contact->address) {
                $html .= '<div class="contact-detail"><strong>Address:</strong> ' . htmlspecialchars($contact->address) . '</div>';
            }
            
            $statusClass = $contact->is_active ? 'status-active' : 'status-inactive';
            $statusText = $contact->is_active ? 'Active' : 'Inactive';
            $html .= '<div class="contact-detail"><strong>Status:</strong> <span class="' . $statusClass . '">' . $statusText . '</span></div>';
            
            if ($contact->tags && $contact->tags->count() > 0) {
                $html .= '<div class="contact-tags">';
                $html .= '<strong>Tags:</strong> ';
                foreach ($contact->tags as $tag) {
                    $html .= '<span class="tag">' . htmlspecialchars($tag->name) . '</span>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
            
            $html .= '</div>';
        }

        // Footer
        $html .= '<div class="footer">';
        $html .= 'Community Directory Report - ' . htmlspecialchars($brandInfo['name']) . '<br>';
        $html .= 'Generated on ' . date('F j, Y \a\t g:i A');
        $html .= '</div>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate actual PDF content
     */
    private function generatePdfContent(Collection $contacts, array $brandInfo): string
    {
        // Create a more robust PDF structure
        $objects = [];
        $objectOffsets = [];
        
        // Object 1: Catalog
        $objects[1] = "1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n";
        
        // Object 2: Pages
        $objects[2] = "2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n";
        
        // Object 3: Page
        $objects[3] = "3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n/Resources <<\n/Font <<\n/F1 5 0 R\n>>\n>>\n>>\nendobj\n";
        
        // Generate content
        $content = $this->generatePdfPageContent($contacts, $brandInfo);
        $contentLength = strlen($content);
        
        // Object 4: Content stream
        $objects[4] = "4 0 obj\n<<\n/Length $contentLength\n>>\nstream\n$content\nendstream\nendobj\n";
        
        // Object 5: Font
        $objects[5] = "5 0 obj\n<<\n/Type /Font\n/Subtype /Type1\n/BaseFont /Helvetica\n>>\nendobj\n";
        
        // Build PDF
        $pdf = "%PDF-1.4\n";
        $currentOffset = strlen($pdf);
        
        // Add objects and track offsets
        for ($i = 1; $i <= 5; $i++) {
            $objectOffsets[$i] = $currentOffset;
            $pdf .= $objects[$i];
            $currentOffset = strlen($pdf);
        }
        
        // Cross-reference table
        $xrefOffset = $currentOffset;
        $pdf .= "xref\n0 6\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $objectOffsets[$i]);
        }
        
        // Trailer
        $pdf .= "trailer\n<<\n/Size 6\n/Root 1 0 R\n>>\n";
        $pdf .= "startxref\n$xrefOffset\n%%EOF";
        
        return $pdf;
    }

    /**
     * Generate PDF page content
     */
    private function generatePdfPageContent(Collection $contacts, array $brandInfo): string
    {
        $content = "BT\n";
        
        // Title
        $content .= "/F1 18 Tf\n";
        $content .= "50 750 Td\n";
        $title = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $brandInfo['name'] . " - Community Directory");
        $content .= "($title) Tj\n";
        
        // Subtitle
        $content .= "/F1 12 Tf\n";
        $content .= "0 -25 Td\n";
        $generated = "Generated: " . date('Y-m-d H:i:s');
        $content .= "($generated) Tj\n";
        
        $content .= "0 -15 Td\n";
        $totalContacts = "Total Contacts: " . $contacts->count();
        $content .= "($totalContacts) Tj\n";
        
        // Separator
        $content .= "0 -25 Td\n";
        $content .= "(________________________________________________) Tj\n";
        
        // Contacts (limit to prevent PDF size issues)
        $y = 680;
        $contactCount = 0;
        foreach ($contacts->take(15) as $contact) {
            if ($contactCount >= 15) break; // Safety limit
            
            $content .= "/F1 12 Tf\n";
            $content .= "0 -25 Td\n";
            $name = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $contact->name);
            $content .= "($name) Tj\n";
            
            $content .= "/F1 10 Tf\n";
            $content .= "0 -15 Td\n";
            $phone = "Phone: " . str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $contact->phone);
            $content .= "($phone) Tj\n";
            
            if ($contact->email) {
                $content .= "0 -12 Td\n";
                $email = "Email: " . str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $contact->email);
                $content .= "($email) Tj\n";
            }
            
            $content .= "0 -12 Td\n";
            $type = "Type: " . ucfirst($contact->type);
            $content .= "($type) Tj\n";
            
            if ($contact->designation) {
                $content .= "0 -12 Td\n";
                $designation = "Role: " . str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $contact->designation);
                $content .= "($designation) Tj\n";
            }
            
            $content .= "0 -12 Td\n";
            $status = "Status: " . ($contact->is_active ? 'Active' : 'Inactive');
            $content .= "($status) Tj\n";
            
            $contactCount++;
            $y -= 100;
            if ($y < 100) break; // Prevent page overflow
        }
        
        // Footer
        $content .= "0 -30 Td\n";
        $content .= "/F1 8 Tf\n";
        $footer = "Report generated by " . $brandInfo['name'];
        $content .= "($footer) Tj\n";
        
        $content .= "ET\n";
        return $content;
    }
}