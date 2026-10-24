# System Architecture & Database Design
## Marriage Registration & Verification System

### 1. Database Entity-Relationship (ER) Diagram
Target Database: `marriage_project`

```mermaid
erDiagram
    kazi_license_counters {
        string district_prefix PK
        int last_serial
    }

    kazi_users {
        int kazi_id PK
        string full_name
        string mobile_no UK
        string email
        string password
        string division
        string district
        string upazila
        text full_address
        string license_no UK
        enum status
        timestamp created_at
    }

    citizens {
        int citizen_id PK
        enum citizen_type
        string full_name
        string father_name
        string mother_name
        date date_of_birth
        enum gender
        string religion
        string national_id UK
        string passport_no
        string nationality
        string mobile_no
        string division
        string district
        string upazila
        text full_address
        string photo
        int created_by_kazi_id
        timestamp created_at
    }

    marriages {
        int marriage_id PK
        string registration_number UK
        date marriage_date
        string religion
        int husband_citizen_id
        enum husband_identity_type
        string husband_identity_no
        string husband_name
        int wife_citizen_id
        enum wife_identity_type
        string wife_identity_no
        string wife_name
        int kazi_id
        string kazi_license_no
        string kazi_name
        string witness1_name
        enum witness1_identity_type
        string witness1_identity_no
        string witness1_mobile
        string witness2_name
        enum witness2_identity_type
        string witness2_identity_no
        string witness2_mobile
        decimal denmohor_amount
        decimal registration_fee
        enum status
        timestamp created_at
    }

    payments {
        int payment_id PK
        string receipt_no UK
        enum service_category
        string reference_no
        decimal fee_amount
        decimal tax_amount
        decimal total_amount
        date payment_date
        enum payment_status
        string kazi_license_no
        timestamp created_at
    }

    divorce_records {
        int divorce_id PK
        string case_reference UK
        string marriage_registration_number
        int marriage_id
        string husband_name
        string wife_name
        enum applicant_party
        string process_category
        date notice_date
        date effective_date
        enum process_status
        text comments
        string supporting_document
        int kazi_id
        string kazi_license_no
        timestamp created_at
    }

    admin_users {
        int admin_id PK
        string username UK
        string password
        string full_name
        string email
        string role
        enum status
        timestamp created_at
    }

    kazi_users ||--o{ citizens : "registers"
    kazi_users ||--o{ marriages : "solemnizes"
    kazi_users ||--o{ divorce_records : "records"
    marriages ||--o{ payments : "triggers"
    marriages ||--o{ divorce_records : "linked to"
```

---

### 2. Kazi Registration & Auto-License Generation Workflow

```mermaid
sequenceDiagram
    autonumber
    actor Kazi as Registering Kazi
    participant Page as kazi/signup.php
    participant Handler as kazi/save_signup.php
    participant Counter as kazi_license_counters
    participant DB as kazi_users

    Kazi->>Page: Fill form (Name, 11-digit Mobile, Division, District, Upazila, Pwd)
    Page->>Handler: POST form submission with CSRF token
    Handler->>Handler: Validate inputs & location hierarchy
    Handler->>Counter: Atomic increment on district prefix (e.g. Dha)
    Counter-->>Handler: Return next sequential serial (e.g. 000002)
    Handler->>Handler: Format license (e.g. Dha_000002)
    Handler->>DB: Insert Kazi record with hashed password
    DB-->>Handler: Record created
    Handler-->>Kazi: Display success confirmation with generated license
```

---

### 3. Marriage Solemnization & Anti-Conflict Workflow

```mermaid
flowchart TD
    Start([Kazi Opens Marriage Registration Form]) --> InputID[Input Husband & Wife Identity Numbers]
    InputID --> Lookup[Auto-lookup Citizen details via AJAX]
    Lookup --> DiffPerson{Husband and Wife different people?}
    DiffPerson -- No --> Err1[Error: Same person selected]
    DiffPerson -- Yes --> GenderChk{Husband is Male & Wife is Female?}
    GenderChk -- No --> Err2[Error: Gender mismatch]
    GenderChk -- Yes --> ConflictChk{Either party has an active marriage?}
    ConflictChk -- Yes --> Err3[Error: Conflicting active marriage detected]
    ConflictChk -- No --> WitChk{Valid witnesses and different individuals?}
    WitChk -- No --> Err4[Error: Invalid witness records]
    WitChk -- Yes --> Calc[Calculate Fee: Denmohor rate / Fixed fee]
    Calc --> Txn[Start Database Transaction]
    Txn --> InsM[Insert into marriages: status = Active]
    InsM --> InsP[Insert into payments: status = Paid]
    InsP --> Commit[Commit Transaction]
    Commit --> Cert[Generate Printable Marriage Certificate]
```
