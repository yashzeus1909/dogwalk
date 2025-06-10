import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { X } from "lucide-react";
import type { Walker, InsertBooking } from "@shared/schema";
import { apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";

interface BookingModalProps {
  walker: Walker | null;
  isOpen: boolean;
  onClose: () => void;
}

const bookingSchema = z.object({
  dogName: z.string().min(1, "Dog's name is required"),
  dogSize: z.enum(["small", "medium", "large"], {
    required_error: "Please select dog size",
  }),
  date: z.string().min(1, "Date is required"),
  time: z.enum(["morning", "afternoon", "evening"], {
    required_error: "Please select a time slot",
  }),
  duration: z.number().min(30, "Duration must be at least 30 minutes"),
  instructions: z.string().optional(),
  phone: z.string().min(10, "Valid phone number is required"),
  email: z.string().email("Valid email is required"),
});

type BookingFormData = z.infer<typeof bookingSchema>;

export default function BookingModal({ walker, isOpen, onClose }: BookingModalProps) {
  const [selectedDuration, setSelectedDuration] = useState<number>(30);
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const form = useForm<BookingFormData>({
    resolver: zodResolver(bookingSchema),
    defaultValues: {
      dogName: "",
      dogSize: undefined,
      date: "",
      time: undefined,
      duration: 30,
      instructions: "",
      phone: "",
      email: "",
    },
  });

  const createBookingMutation = useMutation({
    mutationFn: async (data: InsertBooking) => {
      const response = await apiRequest("POST", "/api/bookings", data);
      return response.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/bookings"] });
      toast({
        title: "Booking Confirmed!",
        description: "Your dog walking service has been booked successfully. A confirmation email has been sent to you.",
      });
      onClose();
      form.reset();
    },
    onError: () => {
      toast({
        title: "Booking Failed",
        description: "Something went wrong. Please try again.",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: BookingFormData) => {
    if (!walker) return;

    const serviceFee = walker.price * 100; // Convert to cents
    const appFee = Math.round(serviceFee * 0.1); // 10% app fee
    const total = serviceFee + appFee;

    const bookingData: InsertBooking = {
      walkerId: walker.id,
      dogName: data.dogName,
      dogSize: data.dogSize,
      date: data.date,
      time: data.time,
      duration: selectedDuration,
      instructions: data.instructions || "",
      phone: data.phone,
      email: data.email,
      serviceFee,
      appFee,
      total,
      status: "pending",
    };

    createBookingMutation.mutate(bookingData);
  };

  const handleDurationSelect = (duration: number) => {
    setSelectedDuration(duration);
    form.setValue("duration", duration);
  };

  const serviceFee = walker ? walker.price : 0;
  const appFee = Math.round(serviceFee * 0.1);
  const total = serviceFee + appFee;

  // Set minimum date to today
  const today = new Date().toISOString().split('T')[0];

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>Book Walking Service</DialogTitle>
          <Button
            variant="ghost"
            size="icon"
            className="absolute right-4 top-4"
            onClick={onClose}
          >
            <X className="w-4 h-4" />
          </Button>
        </DialogHeader>

        <Form {...form}>
          <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
            <FormField
              control={form.control}
              name="dogName"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Dog's Name</FormLabel>
                  <FormControl>
                    <Input placeholder="Enter your dog's name" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="dogSize"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Dog's Size</FormLabel>
                  <Select onValueChange={field.onChange} defaultValue={field.value}>
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Select size" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectItem value="small">Small (under 25 lbs)</SelectItem>
                      <SelectItem value="medium">Medium (25-60 lbs)</SelectItem>
                      <SelectItem value="large">Large (over 60 lbs)</SelectItem>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />

            <div className="grid grid-cols-2 gap-4">
              <FormField
                control={form.control}
                name="date"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Date</FormLabel>
                    <FormControl>
                      <Input type="date" min={today} {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />

              <FormField
                control={form.control}
                name="time"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel>Time</FormLabel>
                    <Select onValueChange={field.onChange} defaultValue={field.value}>
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Select time" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectItem value="morning">Morning (8-12 PM)</SelectItem>
                        <SelectItem value="afternoon">Afternoon (12-5 PM)</SelectItem>
                        <SelectItem value="evening">Evening (5-8 PM)</SelectItem>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>

            <div>
              <FormLabel className="text-sm font-medium text-neutral-700 mb-2 block">
                Walk Duration
              </FormLabel>
              <div className="grid grid-cols-3 gap-2">
                {[30, 45, 60].map((duration) => (
                  <Button
                    key={duration}
                    type="button"
                    variant={selectedDuration === duration ? "default" : "outline"}
                    size="sm"
                    onClick={() => handleDurationSelect(duration)}
                    className="transition-colors"
                  >
                    {duration} min
                  </Button>
                ))}
              </div>
            </div>

            <FormField
              control={form.control}
              name="instructions"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Special Instructions</FormLabel>
                  <FormControl>
                    <Textarea
                      placeholder="Any special needs or instructions for your dog..."
                      rows={3}
                      {...field}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="phone"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Phone Number</FormLabel>
                  <FormControl>
                    <Input type="tel" placeholder="Phone number" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="email"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Email Address</FormLabel>
                  <FormControl>
                    <Input type="email" placeholder="Email address" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <div className="bg-neutral-50 rounded-lg p-4 mt-6">
              <div className="flex items-center justify-between text-sm">
                <span className="text-neutral-600">Service Fee:</span>
                <span className="font-medium">${serviceFee.toFixed(2)}</span>
              </div>
              <div className="flex items-center justify-between text-sm mt-1">
                <span className="text-neutral-600">App Fee:</span>
                <span className="font-medium">${appFee.toFixed(2)}</span>
              </div>
              <div className="border-t border-gray-200 mt-2 pt-2 flex items-center justify-between">
                <span className="font-semibold text-neutral-900">Total:</span>
                <span className="font-semibold text-lg text-neutral-900">
                  ${total.toFixed(2)}
                </span>
              </div>
            </div>

            <div className="flex space-x-3 mt-6">
              <Button
                type="button"
                variant="outline"
                className="flex-1"
                onClick={onClose}
              >
                Cancel
              </Button>
              <Button
                type="submit"
                className="flex-1"
                disabled={createBookingMutation.isPending}
              >
                {createBookingMutation.isPending ? "Booking..." : "Confirm Booking"}
              </Button>
            </div>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  );
}
