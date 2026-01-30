# Requirements Document

## Introduction

This document specifies the requirements for enhancing the admin panel of a Laravel application with Vue.js frontend that serves both web users and Android app via API. The enhancements focus on settings configuration, announcements management, community directory improvements, frontend authentication, and business registration features.

## Glossary

- **Admin_Panel**: The administrative interface for managing application settings and content
- **Maintenance_Mode**: A system state that displays a maintenance page to users while allowing admin access
- **Community_Directory**: A searchable directory of contacts categorized by profession, skill, business, or service
- **Announcement_System**: A content management system for displaying announcements with image support
- **Settings_API**: The API endpoint that manages application configuration settings
- **Media_Table**: Database table storing file metadata and relationships to other entities
- **Tags_System**: A categorization system for organizing contacts by profession, skill, business, or service
- **Export_System**: Functionality to generate downloadable files in CSV, Excel, or PDF formats
- **Registration_System**: User account creation functionality with validation
- **Android_API**: API endpoints that serve the mobile application

## Requirements

### Requirement 1: Maintenance Mode Management

**User Story:** As an administrator, I want to enable maintenance mode, so that I can perform system updates while informing users about the temporary unavailability.

#### Acceptance Criteria

1. WHEN maintenance mode is enabled in settings, THE Frontend SHALL display a maintenance page instead of normal content
2. WHEN maintenance mode is enabled, THE Settings_API SHALL return maintenance_mode as true and include maintenance_note in JSON responses
3. WHEN maintenance mode is disabled, THE Frontend SHALL display normal application content
4. THE Maintenance_Page SHALL include proper structure and branding elements
5. THE Android_API SHALL receive maintenance status information for mobile app compatibility

### Requirement 2: Self Registration Control

**User Story:** As an administrator, I want to control user self-registration, so that I can manage who can create accounts on the platform.

#### Acceptance Criteria

1. WHEN "Allow Self Registration" is enabled, THE Frontend SHALL display the registration form and "Create Account" label on login form
2. WHEN "Allow Self Registration" is disabled, THE Frontend SHALL hide the registration form and remove "Create Account" option
3. THE Settings_API SHALL provide the current registration permission status
4. THE System SHALL remove "Last updated: Just now" label from settings display

### Requirement 3: Multiple Image Announcement Support

**User Story:** As an administrator, I want to add multiple images to announcements, so that I can provide richer visual content to users.

#### Acceptance Criteria

1. WHEN creating or editing an announcement, THE Admin_Panel SHALL allow adding multiple images
2. WHEN images are uploaded, THE System SHALL resize them and store metadata in the Media_Table
3. THE System SHALL organize uploaded images in structured folders following existing patterns
4. WHEN displaying announcements, THE Frontend SHALL show all associated images
5. THE Preview_System SHALL implement lightgallery for responsive image viewing

### Requirement 4: Community Directory Form Enhancement

**User Story:** As an administrator, I want an improved contact form interface, so that I can efficiently manage community directory entries.

#### Acceptance Criteria

1. THE Contact_Form SHALL display all fields in a medium-sized modal with proper alignment
2. THE Form SHALL have reduced spacing between input fields for better visual density
3. THE Form SHALL be scrollable on mobile devices when content exceeds viewport
4. THE Image_Upload_UI SHALL be consistent across all application forms
5. THE Form SHALL provide clear image preview functionality

### Requirement 5: Community Directory Export System

**User Story:** As an administrator, I want to export community directory data, so that I can share contact information in various formats.

#### Acceptance Criteria

1. THE Export_System SHALL provide CSV, Excel, and PDF format options
2. WHEN exporting, THE System SHALL include all contact details in the output
3. THE Export_Header SHALL contain Brand Name, Tagline, Logo, Support Phone, and Support Email
4. THE PDF_Footer SHALL display "currentpage/allpages" format (e.g., 1/20)
5. THE PDF_Pages SHALL include a watermark of the brand logo with light opacity

### Requirement 6: Tags Integration for Community Directory

**User Story:** As an administrator, I want to manage contact tags, so that I can categorize community members by profession, skill, business, or service.

#### Acceptance Criteria

1. THE Admin_Panel SHALL allow adding tags from the Tags_System (Doctors, Groceries, Plumbers, Dancers, etc.)
2. THE Tags SHALL reference existing tags migration structure
3. WHEN assigning tags, THE System SHALL maintain relationships between contacts and tags
4. THE Tags SHALL support categories: profession, skill, business, service
5. THE System SHALL validate tag assignments against existing tag definitions

### Requirement 7: Frontend Authentication Enhancement

**User Story:** As a user, I want a consistent authentication experience, so that I can easily access login and registration features.

#### Acceptance Criteria

1. THE Login_Page SHALL use guest layout header and footer
2. THE Registration_Page SHALL match the login layout structure
3. THE Registration_Form SHALL include Full name, Email, Password, Confirm password, and Mobile number fields
4. THE Email_Field SHALL display note "this will be used for login"
5. THE System SHALL implement both server-side and client-side validation for registration

### Requirement 8: Community Directory Frontend Improvements

**User Story:** As a user, I want to browse and search the community directory, so that I can find relevant contacts and services.

#### Acceptance Criteria

1. THE System SHALL rename "Contact Directory" to "Community Directory" in all files, routes, and frontend references
2. THE Directory SHALL implement tag-based searching to fetch contacts related to selected tags
3. THE Filter_Interface SHALL display a filter icon next to category icons
4. WHEN filter is clicked, THE System SHALL display a scrollable list of all available tags
5. THE Tag_Selection SHALL work like existing doctor/plumber filtering functionality

### Requirement 9: Contact Detail Page Structure

**User Story:** As a user, I want to view detailed contact information, so that I can access comprehensive details about community members.

#### Acceptance Criteria

1. THE Contact_Detail_Page SHALL include breadcrumb navigation
2. THE Page_Content SHALL display in a container starting below the logo section
3. THE Left_Side SHALL show contact image, name, and "Call Now" button
4. THE Right_Side SHALL display all user details including skills and blood group
5. THE Page_Layout SHALL be responsive and mobile-friendly

### Requirement 10: Community Directory Pagination

**User Story:** As a user, I want to navigate through community directory pages, so that I can browse all available contacts efficiently.

#### Acceptance Criteria

1. THE Directory SHALL display 20 contacts per page
2. THE Pagination_UI SHALL be consistent with existing application patterns
3. THE System SHALL maintain search and filter state across page navigation
4. THE Pagination SHALL display current page and total pages information
5. THE Navigation SHALL work seamlessly on both web and mobile interfaces

### Requirement 11: Announcements Frontend Enhancement

**User Story:** As a user, I want to browse announcements with search and filtering capabilities, so that I can find relevant information quickly.

#### Acceptance Criteria

1. THE Announcements_Page SHALL use the same UI structure as community directory
2. THE System SHALL implement search functionality for announcements
3. THE System SHALL provide date-wise filtering options
4. THE Page SHALL display 20 announcements per page with pagination
5. THE Listing_Structure SHALL match community directory for consistency

### Requirement 12: Business Registration Enhancement

**User Story:** As a user, I want to register my business, so that I can be included in the community directory.

#### Acceptance Criteria

1. THE Register_Business_Button SHALL include a store icon with proper spacing
2. THE Business_Registration_Form SHALL match login form UI structure
3. THE Form SHALL maintain UI consistency with other application forms
4. THE System SHALL validate business registration data appropriately
5. THE Registration_Process SHALL integrate with existing user management system

### Requirement 13: API Compatibility Maintenance

**User Story:** As a system architect, I want to maintain API compatibility, so that both web frontend and Android application continue to function correctly.

#### Acceptance Criteria

1. THE System SHALL ensure all APIs work for both frontend and Android application
2. THE Media_Table SHALL be used for all image storage operations
3. THE System SHALL maintain existing database relationships
4. THE APIs SHALL follow current application patterns and conventions
5. THE System SHALL implement proper error handling and validation for all endpoints

### Requirement 14: Image Storage Consistency

**User Story:** As a system administrator, I want consistent image handling, so that all media files are properly organized and accessible.

#### Acceptance Criteria

1. THE System SHALL use the Media_Table for all image storage operations
2. THE Image_Upload SHALL resize images appropriately for web and mobile display
3. THE File_Organization SHALL follow existing folder structure patterns
4. THE Image_Preview SHALL be consistent across all application features
5. THE System SHALL maintain proper file metadata including mime_type, file_size, and uploaded_by information