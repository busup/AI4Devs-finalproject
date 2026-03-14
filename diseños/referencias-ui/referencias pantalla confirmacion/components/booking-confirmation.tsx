"use client"

import { useState } from "react"
import { Check, Calendar, Download, Wallet, ArrowRight, MapPin, HelpCircle, ChevronLeft } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Card } from "@/components/ui/card"

interface BookingDetails {
  bookingId: string
  ticketRef: string
  routeId: string
  seatNumber: string
  travelDate: string
  passenger: string
  employeeId: string
  outbound: {
    time: string
    from: string
    to: string
  }
  returnTrip: {
    time: string
    from: string
    to: string
  }
}

const mockBooking: BookingDetails = {
  bookingId: "RT-84920",
  ticketRef: "RT-84920-A12X",
  routeId: "EXPRESS-042",
  seatNumber: "12A (Window)",
  travelDate: "Dec 01, 2023",
  passenger: "Alex Rivers",
  employeeId: "#88219",
  outbound: {
    time: "08:00",
    from: "Home",
    to: "Work",
  },
  returnTrip: {
    time: "17:30",
    from: "Work",
    to: "Home",
  },
}

function QRCode() {
  return (
    <div className="bg-primary p-4 rounded-lg">
      <div className="bg-card p-3 rounded">
        <div className="flex flex-col items-center gap-2">
          <div className="flex items-center gap-1 text-xs text-muted-foreground">
            <MapPin className="w-3 h-3" />
            <span>Route</span>
          </div>
          <p className="font-semibold text-foreground">Booking</p>
          <div className="grid grid-cols-8 gap-0.5 w-24 h-24">
            {Array.from({ length: 64 }).map((_, i) => (
              <div
                key={i}
                className={`aspect-square ${
                  Math.random() > 0.5 ? "bg-foreground" : "bg-card"
                }`}
              />
            ))}
          </div>
          <div className="flex gap-1 mt-1">
            {[...Array(5)].map((_, i) => (
              <div key={i} className="w-1 h-3 bg-foreground rounded-sm" />
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}

function RouteMap() {
  return (
    <div className="relative w-full h-48 md:h-64 rounded-xl overflow-hidden bg-muted">
      <iframe
        src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.63%2C-23.55&layer=mapnik"
        className="w-full h-full border-0 opacity-60"
        title="Route Map"
      />
      <svg
        className="absolute inset-0 w-full h-full pointer-events-none"
        viewBox="0 0 400 200"
        preserveAspectRatio="xMidYMid slice"
      >
        <path
          d="M 80 150 Q 150 80 200 100 Q 280 130 320 60"
          fill="none"
          stroke="#3fb27f"
          strokeWidth="3"
          strokeDasharray="8 6"
          strokeLinecap="round"
        />
        <circle cx="80" cy="150" r="8" fill="#3fb27f" stroke="white" strokeWidth="2" />
        <circle cx="320" cy="60" r="8" fill="#3fb27f" stroke="white" strokeWidth="2" />
        <circle cx="80" cy="150" r="4" fill="white" />
        <circle cx="320" cy="60" r="4" fill="white" />
      </svg>
      <div className="absolute bottom-3 left-1/2 -translate-x-1/2 bg-accent text-accent-foreground px-4 py-2 rounded-full flex items-center gap-3 text-sm">
        <button className="p-1.5 bg-primary rounded-md">
          <MapPin className="w-4 h-4 text-primary-foreground" />
        </button>
        <button className="p-1.5 hover:bg-muted rounded-md transition-colors">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 9V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z" />
          </svg>
        </button>
        <button className="p-1.5 hover:bg-muted rounded-md transition-colors">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
          </svg>
        </button>
      </div>
    </div>
  )
}

export function BookingConfirmation() {
  const [booking] = useState<BookingDetails>(mockBooking)

  const handleDownloadPDF = () => {
    alert("Downloading PDF ticket...")
  }

  const handleAddToCalendar = () => {
    alert("Adding to calendar...")
  }

  const handleAddToWallet = () => {
    alert("Adding to wallet...")
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Mobile Header */}
      <header className="md:hidden bg-accent text-accent-foreground px-4 py-3 flex items-center gap-3">
        <button className="p-1">
          <ChevronLeft className="w-6 h-6" />
        </button>
        <h1 className="text-lg font-medium text-primary flex-1 text-center pr-7">Confirmation</h1>
      </header>

      <div className="max-w-4xl mx-auto px-4 py-6 md:py-12">
        {/* Success Icon & Title */}
        <div className="flex flex-col items-center mb-6 md:mb-10">
          <div className="w-16 h-16 md:w-20 md:h-20 rounded-full bg-primary/15 flex items-center justify-center mb-4">
            <Check className="w-8 h-8 md:w-10 md:h-10 text-primary" strokeWidth={3} />
          </div>
          <h1 className="text-2xl md:text-3xl font-bold text-foreground text-center">Booking Confirmed!</h1>
          <p className="text-muted-foreground mt-2 text-center">Your seat is secured. Have a safe trip!</p>
        </div>

        <div className="grid md:grid-cols-2 gap-6 md:gap-8">
          {/* Ticket Card */}
          <Card className="overflow-hidden shadow-lg border-0">
            {/* Green Header */}
            <div className="bg-primary text-primary-foreground px-5 py-4 flex justify-between items-start">
              <div>
                <p className="text-xs uppercase tracking-wider opacity-80">Pass Status</p>
                <div className="flex items-center gap-2 mt-1">
                  <span className="w-2 h-2 rounded-full bg-primary-foreground" />
                  <span className="font-semibold">Active Ticket</span>
                </div>
              </div>
              <div className="text-right">
                <p className="text-xs uppercase tracking-wider opacity-80">Employee ID</p>
                <p className="font-semibold mt-1">{booking.employeeId}</p>
              </div>
            </div>

            {/* QR Code Section */}
            <div className="bg-card px-5 py-6 flex flex-col items-center">
              <QRCode />
              <p className="text-sm text-muted-foreground mt-4">{booking.ticketRef}</p>
            </div>

            {/* Booking Details */}
            <div className="bg-card px-5 py-4 border-t border-border">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-xs uppercase tracking-wider text-primary">Route ID</p>
                  <p className="font-semibold text-foreground mt-1">{booking.routeId}</p>
                </div>
                <div className="text-right">
                  <p className="text-xs uppercase tracking-wider text-primary">Seat Number</p>
                  <p className="font-semibold text-foreground mt-1">{booking.seatNumber}</p>
                </div>
                <div>
                  <p className="text-xs uppercase tracking-wider text-primary">Travel Date</p>
                  <p className="font-semibold text-foreground mt-1">{booking.travelDate}</p>
                </div>
                <div className="text-right">
                  <p className="text-xs uppercase tracking-wider text-primary">Passenger</p>
                  <p className="font-semibold text-foreground mt-1">{booking.passenger}</p>
                </div>
              </div>
            </div>

            {/* Trip Times - with ticket notch effect */}
            <div className="relative bg-card">
              <div className="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-8 bg-background rounded-r-full" />
              <div className="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-8 bg-background rounded-l-full" />
              <div className="border-t border-dashed border-border mx-4" />
            </div>

            <div className="bg-card px-5 py-4 space-y-3">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <span className="text-lg font-semibold text-foreground">{booking.outbound.time}</span>
                  <span className="w-2 h-2 rounded-full bg-primary" />
                  <span className="text-muted-foreground">{booking.outbound.from}</span>
                  <ArrowRight className="w-4 h-4 text-muted-foreground" />
                  <span className="text-muted-foreground">{booking.outbound.to}</span>
                </div>
                <span className="text-xs font-medium text-primary border border-primary rounded-full px-3 py-1">
                  OUTBOUND
                </span>
              </div>
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <span className="text-lg font-semibold text-foreground">{booking.returnTrip.time}</span>
                  <span className="w-2 h-2 rounded-full border-2 border-muted-foreground" />
                  <span className="text-muted-foreground">{booking.returnTrip.from}</span>
                  <ArrowRight className="w-4 h-4 text-muted-foreground" />
                  <span className="text-muted-foreground">{booking.returnTrip.to}</span>
                </div>
                <span className="text-xs font-medium text-muted-foreground border border-border rounded-full px-3 py-1">
                  RETURN
                </span>
              </div>
            </div>
          </Card>

          {/* Actions & Map Column */}
          <div className="space-y-6">
            {/* Action Buttons */}
            <div className="grid grid-cols-2 gap-3">
              <Button
                variant="outline"
                className="h-12 gap-2 border-border hover:bg-muted"
                onClick={handleAddToCalendar}
              >
                <Calendar className="w-5 h-5" />
                Calendar
              </Button>
              <Button
                variant="outline"
                className="h-12 gap-2 border-border hover:bg-muted"
                onClick={handleDownloadPDF}
              >
                <Download className="w-5 h-5" />
                PDF Ticket
              </Button>
            </div>

            <Button
              variant="outline"
              className="w-full h-12 gap-2 border-border hover:bg-muted"
              onClick={handleAddToWallet}
            >
              <Wallet className="w-5 h-5" />
              Add to Wallet
            </Button>

            <Button className="w-full h-12 bg-accent hover:bg-accent/90 text-accent-foreground">
              Back to Dashboard
            </Button>

            {/* Route Summary Map */}
            <div className="space-y-3">
              <h3 className="text-xs uppercase tracking-wider text-primary font-medium">Route Summary</h3>
              <RouteMap />
            </div>

            {/* Footer */}
            <p className="text-center text-sm text-muted-foreground">
              Booking ID: #{booking.bookingId} •{" "}
              <button className="text-primary hover:underline inline-flex items-center gap-1">
                Need help?
                <HelpCircle className="w-3 h-3" />
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
