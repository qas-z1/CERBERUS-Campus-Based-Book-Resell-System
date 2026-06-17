# 📚 CampusBook UiTM Tapah

A web-based peer-to-peer marketplace application designed specifically to facilitate secure textbook trading among students at **Universiti Teknologi MARA (UiTM) Tapah Campus**.

---

## 🚀 Project Overview

**CampusBook-UiTM** provides a dedicated platform for university students to list, browse, and exchange textbooks directly with one another. By moving away from general marketplaces like Carousell or Facebook Marketplace, this application ensures a **targeted, campus-specific environment** for academic resources, streamlined communication, and verified transactions between peers.

### 🎯 Problem Statement

Students at UiTM Tapah face challenges when buying and selling used textbooks:
- General marketplaces lack campus-specific filtering
- Difficulty verifying buyer/seller legitimacy
- Unsafe physical meetups for book handovers
- No standardized pricing or condition standards
- Limited discoverability of available textbooks within the campus community

### 💡 Solution

CampusBook provides:
- **Campus-isolated marketplace** — Only UiTM Tapah students can access listings
- **Secure handover protocol** — OTP-based verification during physical exchanges
- **Community trust** — In-campus, verified user profiles
- **Efficient discovery** — Filter by course, semester, condition, and price

---

## ✨ Key Features

### 1. **Real-Time Textbook Listings**
- Users can seamlessly **list textbooks** with details: title, author, course code, condition, price, and images
- **Browse available books** with advanced filtering options:
  - Filter by course code or semester
  - Sort by price (low to high / high to low)
  - Filter by condition (Like New, Good, Fair, Poor)
  - Search by keyword
- Real-time updates using Firebase Cloud Firestore
- **My Listings** dashboard to manage posted books

### 2. **Secure OTP Handover Protocol**
- When a buyer and seller agree on a transaction:
  1. **Seller generates OTP** and shares it with buyer
  2. **Buyer verifies OTP** upon meeting for physical handover
  3. **OTP expires after single use** or 10-minute timeout
  4. **Transaction marked complete** after successful OTP verification
- Prevents no-shows and fraudulent transactions
- Both parties receive confirmation

### 3. **Secure User Authentication**
- Email-based registration with **email verification**
- Secure login with Firebase Authentication
- Password reset functionality
- Session management with secure tokens
- Only verified UiTM students can create accounts (future: institutional email verification)

### 4. **User Profiles & Reputation**
- View seller/buyer profiles with:
  - Display name and avatar
  - Verified status
  - Transaction history
  - User ratings and reviews (1-5 stars)
  - "Completed Transactions" badge
- Build trust within the campus community

### 5. **Messaging & Communication**
- In-app messaging between buyer and seller
- Negotiation of prices and meeting details
- Message history stored in Firestore
- Real-time notifications for new messages

### 6. **Payment Integration** (Optional)
- Support for cash payment (primary method)
- Future: Bank transfer or mobile payment options
- Secure payment processing with Stripe/PayPal

### 7. **Live Database Management**
- Efficient backend structure utilizing Firebase services
- Real-time synchronization across all users
- Automatic timestamp tracking for listings
- Cloud-based image storage for book photos

---

## 🛠️ Technology Stack

### **Frontend**
- **Framework:** React.js / Vue.js (depending on preference)
- **Language:** JavaScript/TypeScript
- **Styling:** Tailwind CSS / Bootstrap
- **State Management:** Redux / Context API
- **Responsive Design:** Mobile-first approach (works on all devices)

### **Backend / Database**
- **Authentication:** Firebase Authentication
- **Database:** Cloud Firestore (NoSQL)
- **File Storage:** Cloud Storage (for book images)
- **Real-time Updates:** Firestore listeners
- **Hosting:** Firebase Hosting (Frontend) + Cloud Functions (Backend logic)

### **Architecture**
- **Client-Server Model** with RESTful API principles
- **Firebase Cloud Functions** for server-side logic:
  - OTP generation and validation
  - Email notifications
  - Transaction processing
  - User verification
- **Firestore Collections:**
  - `users` — User profiles and authentication data
  - `listings` — Active textbook listings
  - `transactions` — Completed/ongoing trades
  - `messages` — In-app chat messages
  - `reviews` — User ratings and feedback
  - `otps` — One-time password records

### **Third-Party Services**
- **Firebase Admin SDK** for backend management
- **Sendgrid / Firebase Email** for notifications
- **Google Maps API** (optional) for location-based meetup suggestions

---

## 📦 Project Structure

```
campusbook-uitm/
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Navbar.jsx
│   │   │   ├── ListingCard.jsx
│   │   │   ├── ListingDetail.jsx
│   │   │   ├── UserProfile.jsx
│   │   │   ├── MessageThread.jsx
│   │   │   ├── OTPVerification.jsx
│   │   │   └── ReviewForm.jsx
│   │   ├── pages/
│   │   │   ├── Home.jsx
│   │   │   ├── Browse.jsx
│   │   │   ├── MyListings.jsx
│   │   │   ├── MyPurchases.jsx
│   │   │   ├── Messages.jsx
│   │   │   ├── Profile.jsx
│   │   │   └── Auth.jsx
│   │   ├── services/
│   │   │   ├── firebase.js
│   │   │   ├── auth.js
│   │   │   ├── listings.js
│   │   │   ├── messaging.js
│   │   │   └── transactions.js
│   │   ├── App.jsx
│   │   └── index.jsx
│   ├── package.json
│   └── .env.example
│
├── backend/
│   ├── functions/
│   │   ├── index.js
│   │   ├── otp.js
│   │   ├── notifications.js
│   │   ├── transactions.js
│   │   └── users.js
│   ├── firestore.rules
│   ├── storage.rules
│   └── package.json
│
├── docs/
│   ├── API_DOCUMENTATION.md
│   ├── DATABASE_SCHEMA.md
│   ├── USER_GUIDE.md
│   └── DEPLOYMENT.md
│
├── README.md
└── .gitignore
```

---

## 🚀 Getting Started

### **Prerequisites**
- Node.js (v14 or higher)
- npm or yarn
- Firebase account (free tier available)
- Modern web browser

---

## 📱 Features in Detail

### **Browsing & Listing**
1. **Browse Textbooks**
   - View all active listings on the homepage
   - Filter by course code, semester, condition
   - Search by book title or author
   - See seller profile and ratings

2. **Create New Listing**
   - Fill in book details (title, author, course code, semester)
   - Select condition (Like New, Good, Fair, Poor)
   - Set price
   - Upload multiple photos
   - Add optional description/notes

### **Trading Mechanism**
1. **Initiate Trade**
   - Browse a listing
   - Click "Interested" or "Message Seller"
   - Start negotiation via in-app messaging

2. **Arrange Meetup**
   - Agree on price and location
   - Schedule pickup time
   - Both parties confirm details

3. **OTP Handover**
   - Seller generates 6-digit OTP
   - Seller shares OTP with buyer (in-person or via message)
   - Buyer verifies OTP in app
   - Transaction marked as complete
   - Both parties leave reviews

### **User Reviews & Ratings**
- After successful trade, users rate each other
- Reviews include:
  - Star rating (1-5)
  - Written comment
  - Timeliness (on-time/late)
  - Condition match (book condition matched listing)
- Public review history builds trust

---

## 🔒 Security Measures

1. **Authentication**
   - Firebase Authentication with email/password
   - Password reset via email
   - Session timeout after inactivity

2. **Data Protection**
   - Firestore Security Rules restrict data access
   - Only users can view/modify their own data
   - Encrypted connection (HTTPS)

3. **OTP Security**
   - Single-use OTP tokens
   - 10-minute expiration
   - Rate-limited OTP generation (max 3 per hour)

4. **User Verification**
   - Email verification on signup
   - Future: UiTM institutional email requirement
   - User reputation system via reviews

5. **Image Security**
   - Images stored in Cloud Storage with access controls
   - Antivirus scanning for uploaded files
   - Automatic image optimization

---

## 📊 Database Schema (Firestore)

### **Users Collection**
```json
{
  "uid": "user_id",
  "email": "student@uitm.edu.my",
  "displayName": "Ahmad Qasthalani",
  "photoURL": "https://...",
  "createdAt": "2024-01-15T10:30:00Z",
  "verificationStatus": "verified",
  "totalRatings": 4.8,
  "totalTransactions": 12,
  "bio": "4th year student, selling extra books",
  "blockedUsers": []
}
```

### **Listings Collection**
```json
{
  "listingId": "listing_123",
  "sellerId": "user_id",
  "title": "Advanced Database Systems",
  "author": "C.J. Date",
  "courseCode": "CS451",
  "semester": "5",
  "condition": "Good",
  "price": 45.00,
  "images": ["https://...", "https://..."],
  "description": "Minimal annotations, all pages intact",
  "status": "available",
  "createdAt": "2024-01-15T10:30:00Z",
  "updatedAt": "2024-01-20T14:22:00Z"
}
```

### **Transactions Collection**
```json
{
  "transactionId": "txn_456",
  "buyerId": "user_id_buyer",
  "sellerId": "user_id_seller",
  "listingId": "listing_123",
  "agreedPrice": 40.00,
  "status": "completed",
  "otp": "123456",
  "otpVerifiedAt": "2024-01-20T15:45:00Z",
  "createdAt": "2024-01-20T14:22:00Z",
  "completedAt": "2024-01-20T15:45:00Z"
}
```

### **Messages Collection**
```json
{
  "messageId": "msg_789",
  "conversationId": "conv_123",
  "senderId": "user_id",
  "recipientId": "user_id",
  "content": "Hi, is this book still available?",
  "timestamp": "2024-01-20T14:23:00Z",
  "read": true
}
```

### **Reviews Collection**
```json
{
  "reviewId": "review_101",
  "transactionId": "txn_456",
  "reviewerId": "buyer_user_id",
  "revieweeId": "seller_user_id",
  "rating": 5,
  "comment": "Great condition, friendly seller!",
  "conditionMatch": true,
  "timeliness": "on-time",
  "createdAt": "2024-01-20T16:00:00Z"
}
```

---

## 🔄 Workflow Example

### **Complete Transaction Flow**

```
1. BROWSE & SELECT
   ├─ User A visits homepage
   ├─ Filters by "Computer Science" courses
   └─ Finds "Advanced Database Systems" by User B ($45)

2. INITIATE INTEREST
   ├─ User A clicks "Message Seller"
   └─ Opens in-app chat with User B

3. NEGOTIATE
   ├─ User A: "Hey, is this still available?"
   ├─ User B: "Yes! Still in great condition"
   ├─ User A: "Can you do $40?"
   └─ User B: "Sure, let's meet tomorrow at library"

4. ARRANGE MEETUP
   ├─ Both agree on time and location
   ├─ Transaction record created in Firestore
   └─ Status: "pending_handover"

5. OTP HANDOVER
   ├─ User B generates OTP: 123456
   ├─ Meets User A at library
   ├─ User B shows OTP verbally or via message
   ├─ User A enters OTP in app to verify
   ├─ Transaction marked: "completed"
   └─ Both receive review prompts

6. LEAVE REVIEWS
   ├─ User A rates User B: ⭐⭐⭐⭐⭐ "Great seller!"
   ├─ User B rates User A: ⭐⭐⭐⭐⭐ "Reliable buyer!"
   ├─ Both reviews become public
   └─ Ratings updated in user profiles
```

---

## 📈 Future Enhancements

- [ ] **Mobile App** — Native iOS/Android versions
- [ ] **Institutional Email Verification** — Automatic UiTM email domain validation
- [ ] **Wishlist Feature** — Save books for future purchase
- [ ] **Advanced Search** — Full-text search across book titles, authors, descriptions
- [ ] **Book Recommendations** — AI suggestions based on course and purchase history
- [ ] **Payment Integration** — Online payment options (Stripe, PayPal)
- [ ] **Analytics Dashboard** — Stats on most traded books, popular courses
- [ ] **Donation Feature** — List books for free exchange
- [ ] **Admin Dashboard** — Moderate listings, manage users, resolve disputes
- [ ] **Multi-Campus Support** — Expand to other UiTM branches

---

## 🤝 Contributing

We welcome contributions from UiTM Tapah students and developers!

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m 'Add YourFeature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a Pull Request

### **Contribution Guidelines**
- Follow the existing code style
- Add comments for complex logic
- Test your changes before submitting
- Update documentation as needed

---

## 📄 License

This project is licensed under the **MIT License** — see the LICENSE file for details.

---

## 👥 Team & Contact

**Developed for:** Universiti Teknologi MARA (UiTM) Tapah Campus

**Contact & Support:**
- 📧 Email: support@campusbook-uitm.com
- 📱 Discord: [Join our community server]
- 🐛 Issue Tracker: [GitHub Issues]
- 💬 FAQ: [Documentation Wiki]

---

## 🙏 Acknowledgments

- **Firebase** for providing excellent backend infrastructure
- **UiTM Tapah Campus** for inspiring this project
- **All contributors** and beta testers who helped shape CampusBook
- **Students** for their valuable feedback and suggestions

---

## 📋 Installation Checklist

Before deploying:
- [ ] Firebase project created and configured
- [ ] Environment variables set up
- [ ] Firestore Security Rules deployed
- [ ] Cloud Functions deployed
- [ ] Frontend environment configured
- [ ] Email service (SendGrid) configured
- [ ] Testing completed on multiple browsers
- [ ] Mobile responsiveness verified
- [ ] Performance optimized
- [ ] Security audit completed

---

## 📚 Resources & Documentation

- [Firebase Documentation](https://firebase.google.com/docs)
- [React Documentation](https://react.dev)
- [Firestore Data Model](https://firebase.google.com/docs/firestore)
- [Cloud Functions Guide](https://firebase.google.com/docs/functions)
- [Our API Documentation](./docs/API_DOCUMENTATION.md)
- [Database Schema](./docs/DATABASE_SCHEMA.md)
- [User Guide](./docs/USER_GUIDE.md)

---

**Happy trading! 📚✨**

---

*Last updated: January 2024*  
*Version: 1.0.0*
