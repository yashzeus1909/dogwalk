import { MailService } from '@sendgrid/mail';
import type { Booking, Walker } from '@shared/schema';

if (!process.env.SENDGRID_API_KEY) {
  throw new Error("SENDGRID_API_KEY environment variable must be set");
}

const mailService = new MailService();
mailService.setApiKey(process.env.SENDGRID_API_KEY);

interface BookingConfirmationData {
  booking: Booking;
  walker: Walker;
}

export async function sendBookingConfirmationEmail(
  data: BookingConfirmationData
): Promise<boolean> {
  try {
    const { booking, walker } = data;
    
    // Format price
    const formatPrice = (cents: number) => `$${(cents / 100).toFixed(2)}`;
    
    // Create email content
    const subject = `Booking Confirmed: ${walker.name} - ${booking.date}`;
    
    const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <style>
          body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
          .container { max-width: 600px; margin: 0 auto; padding: 20px; }
          .header { background: #0ea5e9; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
          .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
          .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; }
          .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
          .detail-label { font-weight: bold; color: #6b7280; }
          .detail-value { color: #111827; }
          .walker-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; }
          .total { background: #0ea5e9; color: white; padding: 15px; border-radius: 8px; text-align: center; font-size: 18px; font-weight: bold; }
          .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="header">
            <h1>🐾 PawWalk Booking Confirmed!</h1>
          </div>
          
          <div class="content">
            <p>Hi there!</p>
            <p>Great news! Your dog walking service has been successfully booked. Here are your booking details:</p>
            
            <div class="booking-details">
              <h3>Booking Information</h3>
              <div class="detail-row">
                <span class="detail-label">Booking ID:</span>
                <span class="detail-value">#${booking.id}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Dog's Name:</span>
                <span class="detail-value">${booking.dogName}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Dog Size:</span>
                <span class="detail-value">${booking.dogSize}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">${booking.date}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">${booking.time}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Duration:</span>
                <span class="detail-value">${booking.duration} minutes</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">${booking.status}</span>
              </div>
              ${booking.instructions ? `
              <div class="detail-row">
                <span class="detail-label">Special Instructions:</span>
                <span class="detail-value">${booking.instructions}</span>
              </div>
              ` : ''}
            </div>
            
            <div class="walker-info">
              <h3>Your Walker</h3>
              <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">${walker.name}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Rating:</span>
                <span class="detail-value">${(walker.rating / 10).toFixed(1)}/5 (${walker.reviewCount} reviews)</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Distance:</span>
                <span class="detail-value">${walker.distance}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">${walker.description}</span>
              </div>
            </div>
            
            <div class="booking-details">
              <h3>Payment Summary</h3>
              <div class="detail-row">
                <span class="detail-label">Service Fee:</span>
                <span class="detail-value">${formatPrice(booking.serviceFee)}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">App Fee:</span>
                <span class="detail-value">${formatPrice(booking.appFee)}</span>
              </div>
            </div>
            
            <div class="total">
              Total Paid: ${formatPrice(booking.total)}
            </div>
            
            <p>Your walker will contact you before the scheduled time. If you have any questions or need to make changes, please contact us.</p>
            
            <div class="footer">
              <p>Thank you for choosing PawWalk!</p>
              <p>Questions? Contact us at support@pawwalk.com</p>
            </div>
          </div>
        </div>
      </body>
      </html>
    `;
    
    const textContent = `
PawWalk Booking Confirmed!

Hi there!

Your dog walking service has been successfully booked. Here are your booking details:

Booking Information:
- Booking ID: #${booking.id}
- Dog's Name: ${booking.dogName}
- Dog Size: ${booking.dogSize}
- Date: ${booking.date}
- Time: ${booking.time}
- Duration: ${booking.duration} minutes
- Status: ${booking.status}
${booking.instructions ? `- Special Instructions: ${booking.instructions}` : ''}

Your Walker:
- Name: ${walker.name}
- Rating: ${(walker.rating / 10).toFixed(1)}/5 (${walker.reviewCount} reviews)
- Distance: ${walker.distance}
- Description: ${walker.description}

Payment Summary:
- Service Fee: ${formatPrice(booking.serviceFee)}
- App Fee: ${formatPrice(booking.appFee)}
- Total Paid: ${formatPrice(booking.total)}

Your walker will contact you before the scheduled time. If you have any questions or need to make changes, please contact us.

Thank you for choosing PawWalk!
Questions? Contact us at support@pawwalk.com
    `;

    await mailService.send({
      to: booking.email,
      from: 'noreply@pawwalk.com', // You can customize this
      subject: subject,
      text: textContent,
      html: htmlContent,
    });

    console.log(`✅ Booking confirmation email sent to ${booking.email}`);
    return true;
  } catch (error) {
    console.error('❌ SendGrid email error:', error);
    return false;
  }
}

export async function sendBookingStatusUpdateEmail(
  data: BookingConfirmationData,
  newStatus: string
): Promise<boolean> {
  try {
    const { booking, walker } = data;
    
    const subject = `Booking Update: ${walker.name} - ${booking.date}`;
    
    const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <style>
          body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
          .container { max-width: 600px; margin: 0 auto; padding: 20px; }
          .header { background: #0ea5e9; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
          .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
          .status-update { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; text-align: center; }
          .status { font-size: 24px; font-weight: bold; color: #0ea5e9; margin: 10px 0; }
          .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="header">
            <h1>🐾 Booking Status Update</h1>
          </div>
          
          <div class="content">
            <p>Hi there!</p>
            <p>We have an update on your booking with ${walker.name}:</p>
            
            <div class="status-update">
              <h3>Booking #${booking.id}</h3>
              <p>Status changed to:</p>
              <div class="status">${newStatus.toUpperCase()}</div>
              <p>Date: ${booking.date} | Time: ${booking.time}</p>
              <p>Dog: ${booking.dogName}</p>
            </div>
            
            <p>If you have any questions about this update, please contact us.</p>
            
            <div class="footer">
              <p>Thank you for choosing PawWalk!</p>
              <p>Questions? Contact us at support@pawwalk.com</p>
            </div>
          </div>
        </div>
      </body>
      </html>
    `;

    await mailService.send({
      to: booking.email,
      from: 'noreply@pawwalk.com',
      subject: subject,
      html: htmlContent,
    });

    console.log(`✅ Status update email sent to ${booking.email}`);
    return true;
  } catch (error) {
    console.error('❌ SendGrid email error:', error);
    return false;
  }
}