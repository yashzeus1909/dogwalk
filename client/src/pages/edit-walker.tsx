import { useState, useEffect } from "react";
import { useRoute } from "wouter";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Checkbox } from "@/components/ui/checkbox";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { useToast } from "@/hooks/use-toast";
import Header from "@/components/header";
import { Link } from "wouter";

const walkerSchema = z.object({
  name: z.string().min(2, "Name must be at least 2 characters"),
  image: z.string().url("Must be a valid URL").optional().or(z.literal("")),
  rating: z.number().min(0).max(5).default(0),
  reviewCount: z.number().min(0).default(0),
  distance: z.string().optional(),
  price: z.number().min(1, "Price must be at least $1"),
  description: z.string().optional(),
  availability: z.string().optional(),
  badges: z.array(z.string()).default([]),
  backgroundCheck: z.boolean().default(false),
  insured: z.boolean().default(false),
  certified: z.boolean().default(false),
});

type WalkerFormData = z.infer<typeof walkerSchema>;

export default function EditWalker() {
  const [, params] = useRoute("/edit-walker/:id");
  const [selectedBadges, setSelectedBadges] = useState<string[]>([]);
  const { toast } = useToast();
  const queryClient = useQueryClient();

  const walkerId = params?.id ? parseInt(params.id) : null;

  const availableBadges = [
    "Verified", "Insured", "Background Checked", 
    "Dog Walking", "Pet Sitting", "Pet Boarding", 
    "Doggy Daycare", "Grooming", "Training"
  ];

  const { data: walker, isLoading } = useQuery({
    queryKey: ["/api/walkers", walkerId],
    queryFn: async () => {
      const response = await fetch(`/api/walkers/${walkerId}`);
      if (!response.ok) throw new Error('Walker not found');
      return response.json();
    },
    enabled: !!walkerId,
  });

  const form = useForm<WalkerFormData>({
    resolver: zodResolver(walkerSchema),
    defaultValues: {
      name: "",
      image: "",
      rating: 0,
      reviewCount: 0,
      distance: "",
      price: 25,
      description: "",
      availability: "",
      badges: [],
      backgroundCheck: false,
      insured: false,
      certified: false,
    },
  });

  // Update form when walker data is loaded
  useEffect(() => {
    if (walker) {
      const walkerData = walker as any; // Type assertion for compatibility
      form.reset({
        name: walkerData.name || "",
        image: walkerData.image || "",
        rating: (walkerData.rating || 0) / 10, // Convert from database integer to decimal
        reviewCount: walkerData.reviewCount || 0,
        distance: walkerData.distance || "",
        price: walkerData.price || 25,
        description: walkerData.description || "",
        availability: walkerData.availability || "",
        backgroundCheck: walkerData.backgroundCheck || false,
        insured: walkerData.insured || false,
        certified: walkerData.certified || false,
      });

      // Set badges
      let walkerBadges = [];
      if (typeof walkerData.badges === 'string') {
        try {
          walkerBadges = JSON.parse(walkerData.badges);
        } catch {
          walkerBadges = [];
        }
      } else if (Array.isArray(walkerData.badges)) {
        walkerBadges = walkerData.badges;
      }
      setSelectedBadges(walkerBadges);
    }
  }, [walker, form]);

  const updateWalkerMutation = useMutation({
    mutationFn: async (data: WalkerFormData) => {
      const walkerData = {
        ...data,
        badges: selectedBadges,
      };
      
      const response = await fetch(`/api/walkers/${walkerId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(walkerData),
      });
      
      if (!response.ok) {
        throw new Error("Failed to update walker");
      }
      
      return response.json();
    },
    onSuccess: () => {
      toast({
        title: "Success",
        description: "Walker updated successfully!",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/walkers"] });
      queryClient.invalidateQueries({ queryKey: ["/api/walkers", walkerId] });
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to update walker. Please try again.",
        variant: "destructive",
      });
    },
  });

  const deleteWalkerMutation = useMutation({
    mutationFn: async () => {
      const response = await fetch(`/api/walkers/${walkerId}`, {
        method: "DELETE",
      });
      
      if (!response.ok) {
        throw new Error("Failed to delete walker");
      }
      
      return response.json();
    },
    onSuccess: () => {
      toast({
        title: "Success",
        description: "Walker deleted successfully!",
      });
      queryClient.invalidateQueries({ queryKey: ["/api/walkers"] });
      window.location.href = "/";
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to delete walker. Please try again.",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: WalkerFormData) => {
    updateWalkerMutation.mutate(data);
  };

  const handleDelete = () => {
    if (window.confirm("Are you sure you want to delete this walker? This action cannot be undone.")) {
      deleteWalkerMutation.mutate();
    }
  };

  const toggleBadge = (badge: string) => {
    setSelectedBadges(prev => 
      prev.includes(badge) 
        ? prev.filter(b => b !== badge)
        : [...prev, badge]
    );
  };

  if (!walkerId) {
    return (
      <div className="min-h-screen bg-neutral-50">
        <Header />
        <div className="max-w-4xl mx-auto px-4 py-6">
          <div className="text-center">
            <h1 className="text-2xl font-bold text-gray-900 mb-4">Walker Not Found</h1>
            <Link href="/" className="text-blue-600 hover:text-blue-800">← Back to Home</Link>
          </div>
        </div>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="min-h-screen bg-neutral-50">
        <Header />
        <div className="max-w-4xl mx-auto px-4 py-6">
          <div className="text-center">
            <div className="animate-pulse">Loading walker details...</div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-neutral-50">
      <Header />
      
      <main className="max-w-4xl mx-auto px-4 py-6">
        <div className="mb-6">
          <Link href="/" className="text-blue-600 hover:text-blue-800 text-sm">← Back to Home</Link>
        </div>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle className="text-2xl">Edit Walker</CardTitle>
            <Button 
              variant="destructive" 
              onClick={handleDelete}
              disabled={deleteWalkerMutation.isPending}
            >
              {deleteWalkerMutation.isPending ? "Deleting..." : "Delete Walker"}
            </Button>
          </CardHeader>
          <CardContent>
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Name *</FormLabel>
                        <FormControl>
                          <Input placeholder="Walker's full name" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="price"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Price per Hour ($) *</FormLabel>
                        <FormControl>
                          <Input 
                            type="number" 
                            placeholder="25" 
                            {...field}
                            onChange={(e) => field.onChange(Number(e.target.value))}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="image"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Profile Image URL</FormLabel>
                        <FormControl>
                          <Input placeholder="https://example.com/photo.jpg" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="distance"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Distance</FormLabel>
                        <FormControl>
                          <Input placeholder="1.2 miles" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="rating"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Rating (0-5)</FormLabel>
                        <FormControl>
                          <Input 
                            type="number" 
                            step="0.1"
                            min="0" 
                            max="5" 
                            placeholder="4.8" 
                            {...field}
                            onChange={(e) => field.onChange(Number(e.target.value))}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="reviewCount"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Review Count</FormLabel>
                        <FormControl>
                          <Input 
                            type="number" 
                            placeholder="127" 
                            {...field}
                            onChange={(e) => field.onChange(Number(e.target.value))}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </div>

                <FormField
                  control={form.control}
                  name="description"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Description</FormLabel>
                      <FormControl>
                        <Textarea 
                          placeholder="Describe the walker's experience and specialties..." 
                          {...field} 
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <FormField
                  control={form.control}
                  name="availability"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Availability</FormLabel>
                      <FormControl>
                        <Input placeholder="Mon-Fri 9am-5pm" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <div>
                  <FormLabel>Service Badges</FormLabel>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                    {availableBadges.map((badge) => (
                      <div key={badge} className="flex items-center space-x-2">
                        <Checkbox
                          id={badge}
                          checked={selectedBadges.includes(badge)}
                          onCheckedChange={() => toggleBadge(badge)}
                        />
                        <label htmlFor={badge} className="text-sm font-medium">
                          {badge}
                        </label>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <FormField
                    control={form.control}
                    name="backgroundCheck"
                    render={({ field }) => (
                      <FormItem className="flex items-center space-x-2">
                        <FormControl>
                          <Checkbox
                            checked={field.value}
                            onCheckedChange={field.onChange}
                          />
                        </FormControl>
                        <FormLabel>Background Check</FormLabel>
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="insured"
                    render={({ field }) => (
                      <FormItem className="flex items-center space-x-2">
                        <FormControl>
                          <Checkbox
                            checked={field.value}
                            onCheckedChange={field.onChange}
                          />
                        </FormControl>
                        <FormLabel>Insured</FormLabel>
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="certified"
                    render={({ field }) => (
                      <FormItem className="flex items-center space-x-2">
                        <FormControl>
                          <Checkbox
                            checked={field.value}
                            onCheckedChange={field.onChange}
                          />
                        </FormControl>
                        <FormLabel>Certified</FormLabel>
                      </FormItem>
                    )}
                  />
                </div>

                <div className="flex gap-4">
                  <Button 
                    type="submit" 
                    className="flex-1"
                    disabled={updateWalkerMutation.isPending}
                  >
                    {updateWalkerMutation.isPending ? "Updating..." : "Update Walker"}
                  </Button>
                  <Link href="/">
                    <Button type="button" variant="outline">
                      Cancel
                    </Button>
                  </Link>
                </div>
              </form>
            </Form>
          </CardContent>
        </Card>
      </main>
    </div>
  );
}